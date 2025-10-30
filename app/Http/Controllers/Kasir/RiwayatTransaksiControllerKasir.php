<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\TransaksiKas;
use App\Models\KasWarung;
use App\Models\BarangKeluar;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RiwayatTransaksiControllerKasir extends Controller
{
    /**
     * Menampilkan riwayat transaksi kas untuk warung yang dimiliki oleh user yang sedang login.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $search = $request->get('search');
        $perPage = 10;

        // 1. Dapatkan ID KasWarung yang terkait dengan User/Warung yang sedang login.
        $kasWarungIds = KasWarung::whereHas('warung', fn($query) => $query->where('id_user', $userId))
            ->pluck('id');

        // Handle kasus jika tidak ada KasWarung yang ditemukan.
        if ($kasWarungIds->isEmpty()) {
            // Menggunakan page 1 sebagai default.
            $riwayatTransaksi = new LengthAwarePaginator(collect([]), 0, $perPage, 1);
            return view('kasir.riwayat_transaksi.index', compact('riwayatTransaksi', 'search'));
        }

        // 2. Query TransaksiKas: Hanya transaksi dari kas warung user ini.
        $query = TransaksiKas::with([
            'transaksiBarangKeluar.barangKeluar.stokWarung.barang',
            'hutang',
        ])
        ->whereIn('id_kas_warung', $kasWarungIds)
        ->latest(); // Urutkan berdasarkan created_at (tanggal) terbaru

        // Terapkan Filter Pencarian jika ada.
        $query->when($search, function ($q) use ($search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('keterangan', 'like', "%$search%")
                    ->orWhere('metode_pembayaran', 'like', "%$search%")
                    // Cari juga berdasarkan nama barang (untuk transaksi penjualan)
                    ->orWhereHas('transaksiBarangKeluar.barangKeluar.stokWarung.barang', function ($qBarang) use ($search) {
                        $qBarang->where('nama_barang', 'like', "%$search%");
                    });
            });
        });

        // 3. Paginate Query DARI DATABASE (Jauh lebih efisien)
        $riwayatTransaksiPaginator = $query->paginate($perPage);

        // 4. Transformasi Data untuk format tampilan yang seragam.
        $riwayatTransaksi = $riwayatTransaksiPaginator->through(function ($transaksi) {
            return $this->transformTransaksi($transaksi);
        });

        return view('kasir.riwayat_transaksi.index', compact('riwayatTransaksi', 'search'));
    }

    /**
     * Melakukan transformasi objek TransaksiKas ke format standar Riwayat Transaksi.
     * @param TransaksiKas $transaksi
     * @return object
     */
    protected function transformTransaksi($transaksi)
    {
        // Nilai Default
        $detail = $transaksi->keterangan ?? 'Transaksi Kas Umum';
        $jenis_transaksi = 'Kas Umum'; // Default
        $metode_pembayaran = $transaksi->metode_pembayaran ?? 'N/A';

        // --- Logika Penentuan Jenis Transaksi Berdasarkan Nilai $transaksi->jenis ---
        switch ($transaksi->jenis) {
            case 'penjualan barang':
                // Ambil detail nama barang dari relasi
                if ($transaksi->transaksiBarangKeluar->isNotEmpty()) {
                    $barangKeluar = $transaksi->transaksiBarangKeluar->first()->barangKeluar;
                    $barang = optional(optional($barangKeluar->stokWarung))->barang;
                    $nama_barang = optional($barang)->nama_barang ?? 'Barang Tidak Dikenal';
                    $count = count($transaksi->transaksiBarangKeluar);

                    $detail = "Penjualan Barang: $nama_barang" . ($count > 1 ? ' (+ ' . ($count - 1) . ' item lain)' : '');
                }
                $jenis_transaksi = 'Penjualan Barang';
                $metode_pembayaran = $transaksi->metode_pembayaran ?? 'Cash/Transfer';
                break;

            case 'penjualan pulsa':
                // Penjualan pulsa dianggap sebagai transaksi tunggal (tidak punya relasi barang keluar)
                // Keterangan dari DB harusnya sudah mencukupi (e.g., "Penjualan pulsa 5.000 ke...")
                $jenis_transaksi = 'Penjualan Pulsa';
                $metode_pembayaran = $transaksi->metode_pembayaran ?? 'Cash/Transfer';
                break;

            case 'hutang barang':
                // Ambil detail nama barang dari relasi (jika tersedia)
                if ($transaksi->transaksiBarangKeluar->isNotEmpty()) {
                    $barangKeluar = $transaksi->transaksiBarangKeluar->first()->barangKeluar;
                    $barang = optional(optional($barangKeluar->stokWarung))->barang;
                    $nama_barang = optional($barang)->nama_barang ?? 'Barang Tidak Dikenal';
                    $detail = "Piutang Barang: $nama_barang";
                }
                $jenis_transaksi = 'Piutang Barang';
                $metode_pembayaran = 'Piutang';
                break;

            case 'hutang pulsa':
                // Keterangan sudah cukup detail, gunakan keterangan dari DB
                $jenis_transaksi = 'Piutang Pulsa';
                $metode_pembayaran = 'Piutang';
                break;

            case 'masuk':
                // Cek apakah ini Pelunasan Hutang
                if ($transaksi->hutang) {
                    $detail = 'Pelunasan Hutang (ID Hutang: ' . $transaksi->hutang->id . ')';
                    $jenis_transaksi = 'Pelunasan Piutang';
                    $metode_pembayaran = 'Pelunasan';
                } else {
                    // Kas Masuk Tunai/Transfer biasa. Cek apakah ini pulsa (jika tidak ada jenis 'penjualan pulsa')
                    // Kita bisa menggunakan keterangan dari DB untuk mendeteksi pulsa, tapi ini kurang robust.
                    // Jika DB Anda menggunakan 'masuk' untuk pulsa tunai, biarkan Kas Masuk.
                    $jenis_transaksi = 'Kas Masuk';
                    $metode_pembayaran = $transaksi->metode_pembayaran ?? 'Cash';
                }
                break;

            case 'keluar':
                $jenis_transaksi = 'Kas Keluar';
                break;

            // Tambahkan jenis lain jika ada (misal: 'expayet', 'hilang')
            case 'expayet':
            case 'hilang':
                $jenis_transaksi = 'Kerugian Stok';
                break;
        }

        // --- Penyesuaian Nilai Total (Tanda Positif/Negatif) ---

        // Transaksi jenis 'keluar', 'expayet', 'hilang' adalah pengeluaran (kas keluar), maka total negatif.
        $is_pengeluaran = in_array($transaksi->jenis, ['keluar', 'expayet', 'hilang']);
        $total = $is_pengeluaran ? -$transaksi->total : $transaksi->total;

        // Pastikan format total berupa string dengan dua desimal.
        $formatted_total = number_format((float)$total, 2, '.', '');

        return (object) [
            'id_ref' => 'TK-' . $transaksi->id,
            'tanggal' => $transaksi->created_at,
            'jenis_transaksi' => $jenis_transaksi,
            'deskripsi' => $detail,
            'total' => $formatted_total,
            'metode_pembayaran' => $metode_pembayaran,
            'tipe_sumber' => 'TransaksiKas'
        ];
    }
}
