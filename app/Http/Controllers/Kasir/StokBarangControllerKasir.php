<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\Laba;
use App\Models\BarangKeluar;
use App\Models\StokWarung; // Pastikan model ini tersedia
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StokBarangControllerKasir extends Controller
{
    /**
     * Menampilkan daftar stok barang umum di warung.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $userId = Auth::id();
        $today = Carbon::today(); // Ambil tanggal hari ini

        // 1. Ambil Data Stok Barang (TIDAK BERUBAH)
        $stokBarang = StokWarung::with([
            'barang.transaksiBarang' => function ($query) {
                $query->with('areaPembelian')->latest();
            },
            'warung',
            'kuantitas'
        ])
            ->whereHas('warung', function ($query) use ($userId) {
                $query->where('id_user', $userId);
            })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('barang', function ($q) use ($search) {
                    $q->where('nama_barang', 'like', "%$search%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Transformasi Data Stok Barang (TIDAK BERUBAH)
        $stokBarang->getCollection()->transform(function ($stok) {
            // ... (Logika perhitungan harga jual dan kadaluarsa tetap sama) ...
            $stok->stok_saat_ini = $stok->jumlah;

            $transaksi = $stok->barang->transaksiBarang->first();

            if (!$transaksi || $transaksi->jumlah == 0) {
                $stok->harga_jual = 0;
                $stok->tanggal_kadaluarsa = null;
                $stok->kuantitas_list = $stok->kuantitas;
                return $stok;
            }

            $hargaDasarPerSatuan = ($transaksi->harga ?? 0) / max($transaksi->jumlah, 1);

            $markupPercent = optional($transaksi->areaPembelian)->markup ?? 0;
            $hargaSetelahMarkup = $hargaDasarPerSatuan + ($hargaDasarPerSatuan * $markupPercent / 100);

            $laba = Laba::where('input_minimal', '<=', $hargaSetelahMarkup)
                ->where('input_maksimal', '>=', $hargaSetelahMarkup)
                ->first();

            $stok->harga_jual = $laba ? $laba->harga_jual : 0;
            $stok->tanggal_kadaluarsa = $transaksi->tanggal_kadaluarsa;
            $stok->kuantitas_list = $stok->kuantitas->sortByDesc('jumlah');

            return $stok;
        });

        // 2. Ambil Data Barang Keluar HARI INI
        $barangKeluar = BarangKeluar::whereHas('stokWarung.warung', function ($query) use ($userId) {
            $query->where('id_user', $userId);
        })
            // === PERUBAHAN DI SINI: Filter berdasarkan created_at hari ini ===
            ->whereDate('created_at', $today)
            // =================================================================
            ->with([
                'stokWarung.barang',
                'transaksiBarangKeluar',
                'barangHutang.hutang',
            ])
            ->latest()
            ->get();


        // 3. Transformasi Data Barang Keluar untuk status hutang/lunas (TIDAK BERUBAH)
        $barangKeluar->transform(function ($keluar) {
            $keluar->is_hutang = $keluar->barangHutang()->exists();
            $keluar->status_hutang = 'Tidak Terkait Hutang';

            if ($keluar->is_hutang) {
                $hutang = optional($keluar->barangHutang)->hutang;
                if ($hutang) {
                    $keluar->status_hutang = $hutang->status === 'Lunas' ? 'Lunas' : 'Belum Lunas';
                } else {
                    $keluar->status_hutang = 'Belum Lunas (Data Hutang Hilang)';
                }
            }
            return $keluar;
        });

        // Kirim kedua data ke view
        return view('kasir.stok_barang.index', compact('stokBarang', 'barangKeluar', 'search'));
    }



    /**
     * Menampilkan daftar barang masuk (pending/diterima/ditolak) untuk konfirmasi.
     */
    public function barangMasuk(Request $request)
    {
        $status = $request->get('status', 'kirim');
        $search = $request->get('search');

        //         $barangMasuk = BarangMasuk::with([
        //     'transaksiBarang.areaPembelian',
        //     'stokWarung.barang',
        //     'stokWarung.warung.user'
        // ])->get();
        // dd($barangMasuk);
        $barangMasuk = BarangMasuk::with([
            'transaksiBarang.areaPembelian',
            'stokWarung.barang',
            'stokWarung.warung.user'
        ])
            ->whereHas('stokWarung.warung', function ($query) {
                $query->where('id_user', Auth::id());
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%$search%")
                        ->orWhereHas('transaksiBarang', function ($qq) use ($search) {
                            $qq->where('id', 'like', "%$search%");
                        })
                        ->orWhereHas('stokWarung.barang', function ($qq) use ($search) {
                            $qq->where('nama_barang', 'like', "%$search%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Hitung harga final dengan markup
        $barangMasuk->getCollection()->transform(function ($bm) {
            $hargaTotalBeli = $bm->transaksiBarang->harga ?? 0;
            $markupPercent = optional($bm->transaksiBarang->areaPembelian)->markup ?? 0;

            $bm->markup_percent = $markupPercent;

            $bm->harga_final_total = $hargaTotalBeli + ($hargaTotalBeli * $markupPercent / 100);

            $jumlah = max($bm->jumlah ?? 1, 1);
            $bm->harga_final_satuan = $bm->harga_final_total > 0 ? ($bm->harga_final_total / $jumlah) : 0;

            return $bm;
        });
        // dd($barangMasuk);
        return view('kasir.stok_barang.barang_masuk', compact('barangMasuk', 'status', 'search'));
    }

    // Metode konfirmasi barang masuk (perlu diimplementasikan)
    public function konfirmasi(Request $request)
    {
        // Logika untuk menerima atau menolak barang masuk yang dipilih
        // ...
        return redirect()->back()->with('success', 'Status barang berhasil diperbarui!');
    }
}
