<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\Laba;
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

        $stokBarang = StokWarung::with([
            'barang.transaksiBarang' => function ($query) {
                // Urutkan transaksi barang agar yang terbaru (latest) berada di paling atas
                // untuk digunakan sebagai basis harga beli
                $query->with('areaPembelian')->latest();
            },
            'warung',
            'kuantitas'
        ])
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

        $stokBarang->getCollection()->transform(function ($stok) {
            $idWarung = $stok->id_warung;

            // --- 1. Hitung Stok Saat Ini (Sama seperti sebelumnya) ---
            if (!isset($stok->stok_saat_ini)) {
                $stokMasuk = $stok->barangMasuk()
                    ->where('status', 'terima')
                    ->whereHas('stokWarung', fn($q) => $q->where('id_warung', $idWarung))
                    ->sum('jumlah');

                $stokKeluar = $stok->barangKeluar()
                    ->whereHas('stokWarung', fn($q) => $q->where('id_warung', $idWarung))
                    ->sum('jumlah');

                $mutasiMasuk = $stok->mutasiBarang()
                    ->where('status', 'terima')
                    ->whereHas('stokWarung', fn($q) => $q->where('warung_tujuan', $idWarung))
                    ->sum('jumlah');

                $mutasiKeluar = $stok->mutasiBarang()
                    ->where('status', 'terima')
                    ->whereHas('stokWarung', fn($q) => $q->where('warung_asal', $idWarung))
                    ->sum('jumlah');

                $stok->stok_saat_ini = $stokMasuk + $mutasiMasuk - $mutasiKeluar - $stokKeluar;
            }

            // --- 2. Hitung Harga Jual Satuan (Sesuai Logika Sebelumnya) ---
            // Ambil transaksi terbaru (sudah di-eager load dan di-sort latest di atas)
            $transaksi = $stok->barang->transaksiBarang->first();

            if (!$transaksi || $transaksi->jumlah == 0) {
                $stok->harga_jual = 0; // Harga Jual Satuan Dasar
                $stok->tanggal_kadaluarsa = null;
                $stok->kuantitas_list = $stok->kuantitas; // Pastikan kuantitas tetap ada
                return $stok;
            }

            // Harga dasar per satuan (Harga total beli / jumlah unit)
            $hargaDasarPerSatuan = ($transaksi->harga ?? 0) / max($transaksi->jumlah, 1);

            // Markup dari area pembelian
            $markupPercent = optional($transaksi->areaPembelian)->markup ?? 0;
            $hargaSetelahMarkup = $hargaDasarPerSatuan + ($hargaDasarPerSatuan * $markupPercent / 100);

            // Harga jual dasar dari tabel Laba
            $laba = Laba::where('input_minimal', '<=', $hargaSetelahMarkup)
                ->where('input_maksimal', '>=', $hargaSetelahMarkup)
                ->first();

            // **Ini adalah Harga Jual Satuan Dasar**
            $hargaJualDasar = $laba ? $laba->harga_jual : 0;

            $stok->harga_jual = $hargaJualDasar;
            $stok->tanggal_kadaluarsa = $transaksi->tanggal_kadaluarsa;

            // Memastikan koleksi kuantitas dikirim ke view
            // Kuangitas sudah di-eager load, cukup memastikan namanya konsisten jika diperlukan
            $stok->kuantitas_list = $stok->kuantitas->sortByDesc('jumlah');

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
