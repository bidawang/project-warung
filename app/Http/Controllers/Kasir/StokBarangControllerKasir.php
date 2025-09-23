<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\StokWarung; // Pastikan model ini tersedia
use Illuminate\Support\Facades\Auth;

class StokBarangControllerKasir extends Controller
{
    /**
     * Menampilkan daftar stok barang umum di warung.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        // Ambil stok barang milik warung kasir user yang login
        // Tambahkan relasi 'barang.transaksiBarang' untuk mengakses data transaksi
        $stokBarang = StokWarung::with(['barang.transaksiBarang' => function ($query) {
            // Eager load relasi areaPembelian dan urutkan berdasarkan tanggal terbaru
            $query->with('areaPembelian')->latest();
        }, 'warung'])
            ->whereHas('warung', function ($query) {
                $query->where('id_user', Auth::id());
            })
            ->when($search, function ($query) use ($search) {
                $query->whereHas('barang', function ($q) use ($search) {
                    $q->where('nama_barang', 'like', "%$search%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Transform koleksi untuk hitung harga jual dan tambahkan tanggal kadaluarsa
        $stokBarang->getCollection()->transform(function ($stok) {
            // Hitung stok saat ini (jika belum ada properti stok_saat_ini, hitung di sini)
            if (!isset($stok->stok_saat_ini)) {
                $stokMasuk = $stok->barangMasuk()
                    ->where('status', 'terima')
                    ->whereHas('stokWarung', fn($q) => $q->where('id_warung', $stok->id_warung))
                    ->sum('jumlah');

                $stokKeluar = $stok->barangKeluar()
                    ->whereHas('stokWarung', fn($q) => $q->where('id_warung', $stok->id_warung))
                    ->sum('jumlah');

                $mutasiMasuk = $stok->mutasiBarang()
                    ->where('status', 'terima')
                    ->whereHas('stokWarung', fn($q) => $q->where('warung_tujuan', $stok->id_warung))
                    ->sum('jumlah');

                $mutasiKeluar = $stok->mutasiBarang()
                    ->where('status', 'terima')
                    ->whereHas('stokWarung', fn($q) => $q->where('warung_asal', $stok->id_warung))
                    ->sum('jumlah');

                $stok->stok_saat_ini = $stokMasuk + $mutasiMasuk - $mutasiKeluar - $stokKeluar;
            }

            // Ambil transaksiBarang terbaru untuk harga dan tanggal kadaluarsa
            // Menggunakan `first()` pada koleksi yang sudah di-eager load lebih efisien
            $transaksi = $stok->barang->transaksiBarang->first();

            if (!$transaksi) {
                $stok->harga_jual = 0;
                $stok->markup_percent = 0;
                $stok->tanggal_kadaluarsa = null; // Tambahkan properti baru
                return $stok;
            }

            $hargaTotalBeli = $transaksi->harga ?? 0;
            $markupPercent = optional($transaksi->areaPembelian)->markup ?? 0;

            $stok->markup_percent = $markupPercent;
            $stok->harga_jual = $hargaTotalBeli + ($hargaTotalBeli * $markupPercent / 100);
            $stok->tanggal_kadaluarsa = $transaksi->tanggal_kadaluarsa; // Ambil tanggal kadaluarsa dari transaksi terbaru

            return $stok;
        });

        return view('kasir.stok_barang.index', compact('stokBarang', 'search'));
    }

    /**
     * Menampilkan daftar barang masuk (pending/diterima/ditolak) untuk konfirmasi.
     */
    public function barangMasuk(Request $request)
    {
        $status = $request->get('status', 'pending');
        $search = $request->get('search');

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
