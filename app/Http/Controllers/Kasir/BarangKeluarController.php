<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluar;
use App\Models\HargaJual;
use App\Models\StokWarung;
use App\Models\KasWarung;
use App\Models\TransaksiKas;
use App\Models\Hutang;
use App\Models\UangPelanggan;
use App\Models\TransaksiBarangKeluar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $barang_keluar = BarangKeluar::whereHas('stokWarung', function ($q) {
    //         $q->where('id_warung', session('id_warung'));
    //     })
    //         ->with(['stokWarung.barang']) // biar langsung load barangnya juga
    //         ->latest()
    //         ->get();

    //     return view('barangkeluar.index', compact('barang_keluar'));
    // }


    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     $users = User::where('role', 'member')->get();
    //     $stok_warungs = StokWarung::where('id_warung', session('id_warung'))
    //         ->with(['barang.transaksiBarang.areaPembelian', 'kuantitas'])
    //         ->get();

    //     $stok_warungs->transform(function ($stok) {
    //         // Hitung stok saat ini
    //         $stokMasuk = $stok->barangMasuk()
    //             ->where('status', 'terima')
    //             ->whereHas('stokWarung', fn($q) => $q->where('id_warung', session('id_warung')))
    //             ->sum('jumlah');

    //         $stokKeluar = $stok->barangKeluar()
    //             ->whereHas('stokWarung', fn($q) => $q->where('id_warung', session('id_warung')))
    //             ->sum('jumlah');

    //         $mutasiMasuk = $stok->mutasiBarang()
    //             ->where('status', 'terima')
    //             ->whereHas('stokWarung', fn($q) => $q->where('id_warung', session('id_warung')))
    //             ->sum('jumlah');

    //         $mutasiKeluar = $stok->mutasiBarang()
    //             ->where('status', 'keluar')
    //             ->whereHas('stokWarung', fn($q) => $q->where('id_warung', session('id_warung')))
    //             ->sum('jumlah');

    //         $stokSaatIni = $stokMasuk + $mutasiMasuk - $mutasiKeluar - $stokKeluar;
    //         $stok->stok_saat_ini = $stokSaatIni;

    //         // Ambil transaksi terbaru
    //         $transaksi = $stok->barang->transaksiBarang()->latest()->first();

    //         if (!$transaksi) {
    //             $stok->harga_jual = 0;
    //             $stok->kuantitas_list = [];
    //             return $stok;
    //         }

    //         // Harga dasar = total beli / jumlah
    //         $hargaDasar = $transaksi->harga / max($transaksi->jumlah, 1);

    //         // Tambahkan markup dari area pembelian
    //         $markupPercent = optional($transaksi->areaPembelian)->markup ?? 0;
    //         $hargaSatuan = $hargaDasar + ($hargaDasar * $markupPercent / 100);

    //         // Ambil harga_jual dari tabel Laba
    //         $laba = Laba::where('input_minimal', '<=', $hargaSatuan)
    //             ->where('input_maksimal', '>=', $hargaSatuan)
    //             ->first();

    //         $stok->harga_jual = $laba ? $laba->harga_jual : 0;

    //         // Daftar kuantitas (bundle)
    //         $stok->kuantitas_list = $stok->kuantitas->map(fn($k) => [
    //             'jumlah' => $k->jumlah,
    //             'harga_jual' => $k->harga_jual,
    //         ]);

    //         return $stok;
    //     });

    //     return view('barangkeluar.create', compact('stok_warungs', 'users'));
    // }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'items'                     => 'required|array|min:1',
            'items.*.id_stok_warung'     => 'required|exists:stok_warung,id',
            'items.*.jumlah'            => 'required|integer|min:1',
            'items.*.harga'             => 'required|numeric|min:0',

            'jenis'                     => 'required|in:penjualan barang,hutang barang,transfer',
            'id_user_member'            => 'nullable|exists:users,id',
            'keterangan_transfer'       => 'nullable|string',

            'total_harga'               => 'required|numeric|min:0',
            'uang_dibayar'              => 'nullable|numeric|min:0',
            'uang_kembalian'            => 'nullable|numeric|min:0',

            'keterangan'                => 'nullable|string',
            'tenggat'                   => 'nullable|date',
        ]);
// dd($validated);
        $idWarung = session('id_warung');
        if (!$idWarung) {
            return redirect()->route('kasir.kasir')->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        /**
         * GENERATE KETERANGAN DAFTAR BARANG
         */
        $stokIds = collect($validated['items'])->pluck('id_stok_warung');
        $stokDetails = \App\Models\StokWarung::with('barang')
            ->whereIn('id', $stokIds)
            ->get();

        $itemDescriptions = $stokDetails->map(function ($stok) use ($validated) {
            $item = collect($validated['items'])->firstWhere('id_stok_warung', $stok->id);
            $nama = optional($stok->barang)->nama_barang ?? 'Barang';
            return "{$nama} ({$item['jumlah']} pcs)";
        })->implode(', ');

        $prefix = match ($validated['jenis']) {
            'penjualan barang' => 'Penjualan Tunai',
            'transfer'         => 'Transfer (' . ($validated['keterangan_transfer'] ?? 'Bank') . ')',
            'hutang barang'    => 'Hutang Barang',
        };
        $finalKeterangan = $validated['keterangan'] ?? ($prefix . ': ' . $itemDescriptions);

        try {
            DB::beginTransaction();

            // 2. Tentukan Jenis Kas & Metode Pembayaran
            if ($validated['jenis'] === 'transfer') {
                $jenisKasTarget = 'bank';
                $metodePembayaran = 'TF (' . ucfirst($validated['keterangan_transfer'] ?? 'Bank') . ')';
                $jenis = 'penjualan barang';
            } else {
                $jenisKasTarget = 'cash';
                $metodePembayaran = $validated['jenis'] === 'penjualan barang' ? 'cash' : 'hutang';
                $jenis = $validated['jenis'];
            }

            // Ambil data Kas Warung
            $kasWarung = \App\Models\KasWarung::where('id_warung', $idWarung)
                ->where('jenis_kas', $jenisKasTarget)
                ->first();

            if (!$kasWarung) {
                $kasWarung = \App\Models\KasWarung::where('id_warung', $idWarung)
                    ->where('jenis_kas', 'cash')
                    ->firstOrFail();
            }

            // 3. Simpan Transaksi Kas (Header Transaksi)
            $transaksiKas = \App\Models\TransaksiKas::create([
                'id_kas_warung'     => $kasWarung->id,
                'total'             => $validated['total_harga'],
                'metode_pembayaran' => $metodePembayaran,
                'jenis'             => $jenis,
                'keterangan'        => $finalKeterangan,
            ]);

            // 4. Update Saldo Kas (Jika bukan hutang)
            if ($validated['jenis'] === 'penjualan barang' || $validated['jenis'] === 'transfer') {
                $kasWarung->updateSaldo($validated['total_harga'], 'tambah');

                \App\Models\UangPelanggan::create([
                    'transaksi_id'   => $transaksiKas->id,
                    'uang_dibayar'   => $validated['uang_dibayar'] ?? $validated['total_harga'],
                    'uang_kembalian' => $validated['uang_kembalian'] ?? 0,
                ]);
            }

            // 5. Logika Hutang (Jika ada member)
            $hutang = null;
            if ($jenis === 'hutang barang' && !empty($validated['id_user_member'])) {
                $hariIni = now()->day;
                $aturan = \App\Models\AturanTenggat::where('id_warung', $idWarung)
                    ->where('tanggal_awal', '<=', $hariIni)
                    ->where('tanggal_akhir', '>=', $hariIni)
                    ->first();

                $tenggat = $aturan ? now()->addDays($aturan->jatuh_tempo_hari) : now()->addDays(7);

                $hutang = \App\Models\Hutang::create([
                    'id_warung'          => $idWarung,
                    'id_user'            => $validated['id_user_member'],
                    'jumlah_hutang_awal' => $validated['total_harga'],
                    'jumlah_sisa_hutang' => $validated['total_harga'],
                    'tenggat'            => $tenggat,
                    'status'             => 'belum lunas',
                    'keterangan'         => $finalKeterangan,
                ]);
            }

            /**
             * 6. PROSES BARANG KELUAR & HITUNG LABA BERSIH
             */
            $totalLabaTransaksiIni = 0;

            foreach ($validated['items'] as $item) {
                // 1. Ambil data stok fisik
                $stokFisik = \App\Models\StokWarung::find($item['id_stok_warung']);
                if (!$stokFisik) continue;

                // 2. Ambil Harga Jual aktif untuk mendapatkan harga modal
                $hargaAktif = \App\Models\HargaJual::where('id_warung', $idWarung)
                    ->where('id_barang', $stokFisik->id_barang)
                    ->whereNull('periode_akhir')
                    ->latest('periode_awal')
                    ->first();
                

                // 3. Definisikan variabel pendukung (PASTIKAN DI ATAS PENGGUNAANNYA)
                $hargaModalSatuan = $hargaAktif->harga_modal ?? 0;
                $hargaJualSatuan  = $item['harga'];
                $jumlahBarang     = $item['jumlah'];
                // dd($hargaJualSatuan);

                // 4. Hitung Laba untuk item ini
                $labaBersihItem = ($hargaJualSatuan/$jumlahBarang - $hargaModalSatuan) * $jumlahBarang;
// dd($labaBersihItem, $hargaJualSatuan, $hargaModalSatuan, $jumlahBarang);
                // 5. Tambahkan ke akumulasi total laba transaksi
                $totalLabaTransaksiIni += $labaBersihItem;

                // 6. Simpan ke tabel barang_keluar
                $barangKeluar = \App\Models\BarangKeluar::create([
                    'id_stok_warung' => $item['id_stok_warung'],
                    'jumlah'         => $jumlahBarang,
                    'harga_jual'     => $hargaJualSatuan,
                    'laba_bersih'    => $labaBersihItem,
                    'jenis'          => $jenis,
                    'keterangan'     => $finalKeterangan,
                ]);

                // 7. Catat ke tabel pivot
                \App\Models\TransaksiBarangKeluar::create([
                    'id_transaksi_kas' => $transaksiKas->id,
                    'id_barang_keluar' => $barangKeluar->id,
                    'jumlah'           => $jumlahBarang,
                ]);

                // 8. Update statistik & stok
                if ($hargaAktif) {
                    $hargaAktif->increment('barang_terjual', $jumlahBarang);
                }
                $stokFisik->decrement('jumlah', $jumlahBarang);

                // 9. Jika transaksi hutang, catat relasi
                if ($hutang) {
                    \App\Models\BarangHutang::create([
                        'id_hutang'        => $hutang->id,
                        'id_barang_keluar' => $barangKeluar->id,
                    ]);
                }
            }

            /**
             * 7. UPDATE TOTAL LABA BERSIH DI TABEL WARUNG
             */
            $warung = \App\Models\Warung::find($idWarung);
            if ($warung) {
                $warung->increment('laba', $totalLabaTransaksiIni);
            }

            DB::commit();
            return redirect()->route('kasir.kasir')
                ->with('success', 'Transaksi ' . ucfirst($jenis) . ' berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal Simpan Transaksi Kasir: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
