<?php

namespace App\Http\Controllers;

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
        $idWarung = session('id_warung'); // ambil dari session

        // Mutasi Masuk (barang menuju warung ini)
        $mutasiMasuk = MutasiBarang::with(['stokWarung.barang', 'warungAsal', 'warungTujuan'])
            ->where('warung_tujuan', $idWarung)
            ->latest()
            ->get();

        // Mutasi Keluar
        $mutasiKeluarPending = MutasiBarang::with(['stokWarung.barang', 'warungAsal', 'warungTujuan'])
            ->where('warung_asal', $idWarung)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $mutasiKeluarDiterima = MutasiBarang::with(['stokWarung.barang', 'warungAsal', 'warungTujuan'])
            ->where('warung_asal', $idWarung)
            ->where('status', 'terima')
            ->latest()
            ->get();

        $mutasiKeluarDitolak = MutasiBarang::with(['stokWarung.barang', 'warungAsal', 'warungTujuan'])
            ->where('warung_asal', $idWarung)
            ->where('status', 'tolak')
            ->latest()
            ->get();

        return view('mutasibarang.index', compact(
            'mutasiMasuk',
            'mutasiKeluarPending',
            'mutasiKeluarDiterima',
            'mutasiKeluarDitolak'
        ));
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
                        $q->where('id_warung', session('id_warung'));
                    })
                    ->sum('jumlah');

                $mutasiKeluar = $stok->mutasiBarang()
                    ->where('status', 'keluar')
                    ->whereHas('stokWarung', function ($q) {
                        $q->where('id_warung', session('id_warung'));
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
    // $request->validate([
    //     'warung_tujuan' => 'required|exists:warung,id',
    //     'barang'        => 'required|array',
    //     'barang.*.id_stok_warung' => 'required|integer|exists:stok_warung,id',
    //     'barang.*.jumlah' => 'required|integer|min:1',
    //     'keterangan'    => 'nullable|string'
    // ]);

    $warungAsal = session('id_warung'); // ambil dari session
    if (!$warungAsal) {
        return back()->withErrors(['warung' => 'Warung asal tidak ditemukan di session.'])->withInput();
    }

    // Jangan izinkan mutasi ke warung yang sama
    if ($request->warung_tujuan == $warungAsal) {
        return back()->withErrors(['warung_tujuan' => 'Warung tujuan tidak boleh sama dengan warung asal.'])->withInput();
    }
    DB::beginTransaction();
    try {
        $created = 0;
        foreach ($request->barang as $key => $data) {
            // form menggunakan struktur barang[id_stok_warung][...]
            // $key biasanya = id_stok_warung — tapi kita tetap baca id dari data untuk safety
            $idStok = isset($data['id_stok_warung']) ? (int)$data['id_stok_warung'] : (int)$key;
            
            // hanya proses yang dicentang
            if (!isset($data['pilih']) || !$data['pilih']) {
                continue;
            }
            
            $jumlahMutasi = (int) ($data['jumlah'] ?? 0);
            if ($jumlahMutasi <= 0) {
                return back()->withErrors(['barang' => "Jumlah mutasi untuk item {$idStok} harus lebih dari 0."])->withInput();
            }
            
            $stok = StokWarung::with(['barang'])->findOrFail($idStok);
            
            // HITUNG stok saat ini sesuai logika di show()
            $stokMasuk = $stok->barangMasuk()
            ->where('status', 'terima')
            ->sum('jumlah');
            
            $stokKeluar = $stok->barangKeluar()
            ->sum('jumlah');
            
            $mutasiMasuk = $stok->mutasiBarang()
            ->where('status', 'terima')
            ->sum('jumlah');
            
            // catatan: di beberapa kode sebelumnya kamu memakai status 'keluar' untuk mutasi keluar.
            // Jika di implementasimu mutasi keluar hanya tercatat sebagai entry dengan warung_asal = this warung,
            // mungkin cukup hitung semua mutasi yang dibuat dari stok ini. Saya pakai filter 'status' === 'keluar'
            // sesuai contoh sebelumnya; sesuaikan bila implementasimu berbeda.
            $mutasiKeluar = $stok->mutasiBarang()
            ->where('status', 'keluar')
            ->sum('jumlah');
            
            $stokSaatIni = $stokMasuk + $mutasiMasuk - $mutasiKeluar - $stokKeluar;
            
            // validasi stok cukup
            if ($jumlahMutasi > $stokSaatIni) {
                DB::rollBack();
                return back()->withErrors([
                    'barang' => "Jumlah mutasi untuk {$stok->barang->nama_barang} melebihi stok tersedia ({$stokSaatIni})."
                    ])->withInput();
                }
                
                // dd($request->all());
                // Simpan Mutasi (status pending)
                MutasiBarang::create([
                    'id_stok_warung' => $stok->id,
                    'warung_asal'    => $warungAsal,
                'warung_tujuan'  => $request->warung_tujuan,
                'jumlah'         => $jumlahMutasi,
                'keterangan'     => $request->keterangan,
                'status'         => 'pending'
            ]);

            $created++;
            // Jangan decrement stok di sini kecuali kamu menyimpan stok_saat_ini di DB secara nyata
            // dan memang ingin menguranginya segera. Biasanya pengurangan stok dilakukan saat mutasi "diterima".
        }

        DB::commit();

        if ($created === 0) {
            return back()->withErrors(['barang' => 'Tidak ada barang yang dipilih.'])->withInput();
        }

        return redirect()->route('mutasibarang.index')->with('success', 'Mutasi berhasil disimpan (pending).');
    } catch (\Throwable $e) {
        DB::rollBack();
        // logging optional
        Log::error('Mutasi store error: '.$e->getMessage());
        return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan mutasi.'])->withInput();
    }
}



    /**
     * Detail mutasi
     */
    public function show($id)
    {
        $mutasi = MutasiBarang::with(['stokWarung.barang', 'warungAsal', 'warungTujuan'])->findOrFail($id);

        return view('mutasibarang.show', compact('mutasi'));
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
