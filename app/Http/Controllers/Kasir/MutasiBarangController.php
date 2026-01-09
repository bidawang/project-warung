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
use App\Models\HargaJual;
use App\Models\HutangBarangMasuk;

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

    public function konfirmasiMasal(Request $request)
    {
        $ids = $request->ids;
        $action = $request->action;
        $idWarungTujuan = session('id_warung');

        if (!$ids || empty($ids)) {
            return back()->with('error', 'Pilih minimal satu item untuk dikonfirmasi.');
        }

        DB::beginTransaction();
        try {
            foreach ($ids as $id) {
                // 1. Ambil data mutasi dengan relasi stok dan barang
                $mutasi = MutasiBarang::with(['stokWarung.barang'])
                    ->where('id', $id)
                    ->where('warung_tujuan', $idWarungTujuan)
                    ->where('status', 'pending')
                    ->first();

                if (!$mutasi) continue;

                if ($action === 'terima') {
                    // --- LOGIKA HARGA & HUTANG ---

                    // 2. Ambil Harga Modal dari Warung Pengirim (Asal)
                    $hargaJualAsal = HargaJual::where('id_warung', $mutasi->warung_asal)
                        ->where('id_barang', $mutasi->stokWarung->id_barang)
                        ->latest()
                        ->first();

                    $hargaModalUnit = $hargaJualAsal ? $hargaJualAsal->harga_modal : 0;
                    $totalNilaiMutasi = $hargaModalUnit * $mutasi->jumlah;

                    // 3. Update Status Mutasi
                    $mutasi->update(['status' => 'terima']);

                    // --- LOGIKA STOK WARUNG TUJUAN ---

                    // 4. Tambah Stok di Warung Penerima (Tujuan)
                    // (Catatan: Stok Warung Asal sudah dipotong di fungsi store)
                    $stokTujuan = StokWarung::firstOrCreate(
                        [
                            'id_warung' => $idWarungTujuan,
                            'id_barang' => $mutasi->stokWarung->id_barang
                        ],
                        ['jumlah' => 0]
                    );
                    $stokTujuan->increment('jumlah', $mutasi->jumlah);

                    // --- LOGIKA HUTANG (PIUTANG PENGIRIM & HUTANG PENERIMA) ---

                    // 5. Catat Piutang untuk Warung Pengirim (Asal)
                    // Nilai NEGATIF pada HutangBarangMasuk berarti mengurangi kewajiban / menambah piutang
                    HutangBarangMasuk::create([
                        'id_warung' => $mutasi->warung_asal,
                        'id_mutasi_barang' => $mutasi->id,
                        'total' => round($totalNilaiMutasi) * -1,
                        'jumlah_unit' => $mutasi->jumlah,
                        'status' => 'lunas',
                        'keterangan' => 'Mutasi Keluar ke Warung ID: ' . $idWarungTujuan
                    ]);

                    // 6. Catat Hutang untuk Warung Penerima (Tujuan)
                    // Nilai POSITIF pada HutangBarangMasuk berarti menambah kewajiban
                    HutangBarangMasuk::create([
                        'id_warung' => $idWarungTujuan,
                        'id_mutasi_barang' => $mutasi->id,
                        'total' => round($totalNilaiMutasi),
                        'jumlah_unit' => $mutasi->jumlah,
                        'status' => 'belum lunas',
                        'keterangan' => 'Hutang Mutasi Masuk dari Warung ID: ' . $mutasi->warung_asal
                    ]);

                    // --- LOGIKA UPDATE HARGA JUAL TUJUAN ---

                    // 7. Ambil Area dan Laba Warung Tujuan untuk menentukan Harga Jual baru
                    $warungTujuanInfo = Warung::with('area.laba')->find($idWarungTujuan);
                    $laba = optional($warungTujuanInfo->area)->laba()
                        ->where('input_minimal', '<=', $hargaModalUnit)
                        ->where('input_maksimal', '>=', $hargaModalUnit)
                        ->first();

                    $hargaJualBaru = optional($laba)->harga_jual ?? 0;

                    HargaJual::create([
                        'id_warung' => $idWarungTujuan,
                        'id_barang' => $mutasi->stokWarung->id_barang,
                        'harga_sebelum_markup' => $hargaJualAsal->harga_sebelum_markup ?? 0,
                        'harga_modal' => round($hargaModalUnit),
                        'harga_jual_range_awal' => $hargaJualBaru,
                        'harga_jual_range_akhir' => $hargaJualBaru,
                        'periode_awal' => now(),
                    ]);
                } else if ($action === 'tolak') {
                    // --- LOGIKA PEMBATALAN ---

                    // 8. Update status mutasi
                    $mutasi->update(['status' => 'tolak']);

                    // 9. Kembalikan stok ke Warung Asal 
                    // Karena stok sudah dikurangi di fungsi store() sebelumnya
                    StokWarung::where('id', $mutasi->id_stok_warung)
                        ->increment('jumlah', $mutasi->jumlah);
                }
            }
// dd('Reached here');
            DB::commit();
            return redirect()->route('mutasibarang.index')
                ->with('success', 'Proses mutasi ' . $action . ' berhasil diselesaikan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Konfirmasi Mutasi Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
