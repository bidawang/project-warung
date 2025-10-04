<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;

use App\Models\MutasiBarang;
use App\Models\StokWarung;
use App\Models\Warung;
use App\Models\Barang;
use App\Models\Laba;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // tambahkan log
use Illuminate\Http\Request;

class MutasiBarangController extends Controller
{

    public function index()
    {
        // Ambil ID warung dari sesi.
        $idWarung = session('id_warung');

        // Jika ID warung tidak ada, kembalikan response yang sesuai.
        if (!$idWarung) {
            return redirect()->back()->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        // Mutasi Masuk: Barang yang ditujukan ke warung ini, terlepas dari statusnya.
        $mutasiMasuk = MutasiBarang::with(['stokWarung.barang', 'warungAsal', 'warungTujuan'])
            ->where('warung_tujuan', $idWarung)
            ->latest()
            ->get();

        // Mutasi Keluar: Gabungkan semua status (pending, terima, tolak) dalam satu query, lalu kelompokkan berdasarkan warung tujuan.
        $mutasiKeluarGrouped = MutasiBarang::with(['stokWarung.barang', 'warungAsal', 'warungTujuan'])
            ->where('warung_asal', $idWarung)
            ->latest()
            ->get()
            ->groupBy('warung_tujuan');
        // dd($mutasiKeluarGrouped);
        return view('mutasibarang.index', compact('mutasiMasuk', 'mutasiKeluarGrouped'));
    }





    /**
     * Form tambah mutasi
     */
    public function create()
    {
        $warungId = session('id_warung'); // ambil warung aktif dari session
        $warung   = Warung::findOrFail($warungId);

        // Ambil semua barang
        $allBarang = Barang::with(['transaksiBarang.areaPembelian'])->get();

        // Ambil stok warung terkait
        $stokWarung = $warung->stokWarung()
            ->with(['barang.transaksiBarang.areaPembelian', 'kuantitas'])
            ->get();

        $stokByBarangId = $stokWarung->keyBy('id_barang');

        // Gabungkan barang dengan stok
        $barangWithStok = $allBarang->map(function ($barang) use ($stokByBarangId) {
            $stok = $stokByBarangId->get($barang->id);

            if ($stok) {
                $stokMasuk = $stok->barangMasuk()
                    ->where('status', 'terima')
                    ->whereHas('stokWarung', function ($q) {
                        $q->where('id_warung', session('id_warung'));
                    })
                    ->sum('jumlah');

                $stokKeluar = $stok->barangKeluar()
                    ->whereHas('stokWarung', function ($q) {
                        $q->where('id_warung', session('id_warung'));
                    })
                    ->sum('jumlah');

                $mutasiMasuk = $stok->mutasiBarang()
                    ->where('status', 'terima')
                    ->whereHas('stokWarung', function ($q) {
                        $q->where('warung_tujuan', session('id_warung'));
                    })
                    ->sum('jumlah');

                $mutasiKeluar = $stok->mutasiBarang()
                    ->where('status', 'terima')
                    ->whereHas('stokWarung', function ($q) {
                        $q->where('warung_asal', session('id_warung'));
                    })
                    ->sum('jumlah');

                $stokSaatIni = $stokMasuk + $mutasiMasuk - $mutasiKeluar - $stokKeluar;


                $transaksi = $stok->barang->transaksiBarang()->latest()->first();

                if (!$transaksi) {
                    $hargaSatuan = 0;
                    $hargaJual   = 0;
                } else {
                    $hargaDasar    = $transaksi->harga / max($transaksi->jumlah, 1);
                    $markupPercent = optional($transaksi->areaPembelian)->markup ?? 0;
                    $hargaSatuan   = $hargaDasar + ($hargaDasar * $markupPercent / 100);

                    $laba = Laba::where('input_minimal', '<=', $hargaSatuan)
                        ->where('input_maksimal', '>=', $hargaSatuan)
                        ->first();
                    $hargaJual = $laba ? $laba->harga_jual : 0;
                }

                $barang->stok_saat_ini  = $stokSaatIni;
                $barang->harga_satuan   = $hargaSatuan;
                $barang->harga_jual     = $hargaJual;
                $barang->id_stok_warung = $stok->id;
            } else {
                $barang->stok_saat_ini  = 0;
                $barang->harga_satuan   = 0;
                $barang->harga_jual     = 0;
                $barang->id_stok_warung = null;
            }

            return $barang;
        });

        // Filter hanya stok > 0
        $barangTersedia = $barangWithStok->filter(function ($barang) {
            return $barang->stok_saat_ini > 0;
        });

        // Ambil semua warung untuk dropdown tujuan (kecuali warung aktif biar ga mutasi ke diri sendiri)
        $warungTujuan = Warung::where('id', '!=', $warungId)->get();
        return view('mutasibarang.create', compact('warung', 'barangTersedia', 'warungTujuan'));
    }




    /**
     * Simpan mutasi baru
     */
    public function store(Request $request)
    {
        $warungAsal = session('id_warung');
        if (!$warungAsal) {
            return back()->withErrors(['warung' => 'Warung asal tidak ditemukan di session.'])->withInput();
        }

        if ($request->warung_tujuan == $warungAsal) {
            return back()->withErrors(['warung_tujuan' => 'Warung tujuan tidak boleh sama dengan warung asal.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $created = 0;
            foreach ($request->barang as $key => $data) {
                if (!isset($data['pilih']) || !$data['pilih']) continue;

                $idStok = $data['id_stok_warung'] ?? $key;
                $jumlahMutasi = (int) ($data['jumlah'] ?? 0);
                if ($jumlahMutasi <= 0) continue;

                $stok = StokWarung::with('barang')->findOrFail($idStok);

                // --- Hitung stok aktual ---
                $stokMasuk = $stok->barangMasuk()->where('status', 'terima')->sum('jumlah');
                $stokKeluar = $stok->barangKeluar()->sum('jumlah');
                $mutasiMasuk = $stok->mutasiBarang()->where('warung_tujuan', $warungAsal)->where('status', 'terima')->sum('jumlah');
                $mutasiKeluar = $stok->mutasiBarang()->where('warung_asal', $warungAsal)->where('status', 'terima')->sum('jumlah');

                $stokSaatIni = $stokMasuk + $mutasiMasuk - $mutasiKeluar - $stokKeluar;

                if ($jumlahMutasi > $stokSaatIni) {
                    DB::rollBack();
                    return back()->withErrors(['barang' => "Jumlah mutasi untuk {$stok->barang->nama_barang} melebihi stok tersedia ({$stokSaatIni})."])->withInput();
                }

                // --- Buat data mutasi barang ---
                MutasiBarang::create([
                    'id_stok_warung' => $stok->id,
                    'warung_asal'    => $warungAsal,
                    'warung_tujuan'  => $request->warung_tujuan,
                    'jumlah'         => $jumlahMutasi,
                    'keterangan'     => $request->keterangan,
                    'status'         => 'pending'
                ]);

                // --- Kurangi stok di warung asal berdasarkan id_warung & id_barang ---
                StokWarung::where('id_warung', $warungAsal)
                    ->where('id_barang', $stok->id_barang)
                    ->decrement('jumlah', $jumlahMutasi);

                $created++;
            }

            DB::commit();

            if ($created === 0) {
                return back()->withErrors(['barang' => 'Tidak ada barang yang dipilih.'])->withInput();
            }

            return redirect()->route('mutasibarang.index')->with('success', 'Mutasi berhasil disimpan (pending).');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Mutasi store error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan mutasi.'])->withInput();
        }
    }



    /**
     * Form edit mutasi
     */
    public function edit($id)
    {
        $mutasi = MutasiBarang::findOrFail($id);
        $stokWarung = StokWarung::with('barang', 'warung')->get();
        $warung = Warung::all();

        return view('mutasibarang.edit', compact('mutasi', 'stokWarung', 'warung'));
    }

    /**
     * Update mutasi
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_stok_warung' => 'required|exists:stok_warung,id',
            'warung_asal'    => 'required|exists:warung,id',
            'warung_tujuan'  => 'required|exists:warung,id|different:warung_asal',
            'jumlah'         => 'required|integer|min:1',
            'status'         => 'required|in:pending,disetujui,ditolak',
            'keterangan'     => 'nullable|string'
        ]);

        $mutasi = MutasiBarang::findOrFail($id);
        $mutasi->update($request->all());

        return redirect()->route('mutasibarang.index')->with('success', 'Mutasi berhasil diperbarui.');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'mutasiBarang' => 'required|array',
            'mutasiBarang.*' => 'exists:mutasi_barang,id',
            'status_baru' => 'required|string|in:terima,tolak',
        ]);
        $ids = $request->input('mutasiBarang');
        $statusBaru = $request->input('status_baru');
        // Update status untuk semua mutasi barang yang dipilih
        MutasiBarang::whereIn('id', $ids)->update(['status' => $statusBaru]);
        return redirect()->route('mutasibarang.index')
            ->with('success', 'Status mutasi barang berhasil diperbarui.');
    }

    /**
     * Hapus mutasi
     */
    public function destroy($id)
    {
        $mutasi = MutasiBarang::findOrFail($id);
        $mutasi->delete();

        return redirect()->route('mutasibarang.index')->with('success', 'Mutasi berhasil dihapus.');
    }
}
