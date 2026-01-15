<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransaksiKas;
use App\Models\Warung; // Perlu model Warung untuk relasi di view
use Carbon\Carbon;

class RiwayatTransaksiControllerAdmin extends Controller
{
    public function index(Request $request)
    {
        // Parameter Filter
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search'); // Tetap di-pass, tapi search di DB dinonaktifkan

        // 1. Dapatkan SEMUA Warung
        $warungs = Warung::with(['kasWarung'])->get();
        // dd($warungs);
        $dataTransaksiPerWarung = collect();

        // 2. Loop setiap Warung untuk mengambil transaksi mereka
        foreach ($warungs as $warung) {
            // Cek apakah warung punya kas warung yang terhubung
            $kasWarung = $warung->kasWarung->first();

            if (!$kasWarung) {
                // Jika tidak ada Kas Warung, lewati atau tambahkan data kosong
                $dataTransaksiPerWarung->push([
                    'id' => $warung->id,
                    'nama_warung' => $warung->nama_warung,
                    'total_kas_warung' => 'N/A',
                    'riwayat_transaksi' => collect(),
                ]);
                continue;
            }

            // Query TransaksiKas untuk warung ini
            $query = TransaksiKas::with([
                'transaksiBarangKeluar.barangKeluar.stokWarung.barang',
                'hutang',
            ])
                ->where('id_kas_warung', $kasWarung->id)
                ->latest(); // Urutkan berdasarkan created_at (tanggal) terbaru

            // Terapkan Filter Tanggal jika ada
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                // Tambahkan 1 hari untuk mencakup seluruh hari terakhir
                $endOfDay = Carbon::parse($endDate)->endOfDay();
                $query->where('created_at', '<=', $endOfDay);
            }

            // Ambil SEMUA data (tanpa paginasi di level ini) untuk ditampilkan per warung.
            // PENTING: Jika data transaksi sangat banyak, model ini tidak disarankan karena boros memori.
            $transaksiWarung = $query->get();

            // Transformasi Data
            $riwayatTransaksi = $transaksiWarung->map(function ($transaksi) use ($warung) {
                // Panggil fungsi transform yang sama
                $transformed = $this->transformTransaksi($transaksi);
                $transformed->id_warung = $warung->id;
                $transformed->nama_warung = $warung->nama_warung;
                return $transformed;
            });

            // Hitung Total Kas Warung (Bisa dari tabel KasWarung atau hitungan transaksi)
            $totalKasWarung = $kasWarung->total_kas ?? $riwayatTransaksi->sum(fn($t) => (float)$t->total);
            // Simpan data per Warung
            $dataTransaksiPerWarung->push([
                'id' => $warung->id,
                'nama_warung' => $warung->nama_warung,
                'total_kas_warung' => $totalKasWarung,
                'riwayat_transaksi' => $riwayatTransaksi,
            ]);
        }

        // dd($totalKasWarung);
        // Kita tidak bisa menggunakan LengthAwarePaginator di sini karena strukturnya.
        // Data yang dikirim: $dataTransaksiPerWarung (Collection of Arrays)
        // dd($dataTransaksiPerWarung);
        return view('admin.riwayat_transaksi.index', compact(
            'dataTransaksiPerWarung',
            'startDate',
            'endDate',
            'search'
        ));
    }

    // (Fungsi transformTransaksi TIDAK BERUBAH, tetap sama seperti di jawaban sebelumnya)
    protected function transformTransaksi($transaksi)
    {
        // ... (Isi fungsi transformTransaksi sama persis seperti di jawaban sebelumnya)
        // Nilai Default
        $detail = $transaksi->keterangan ?? 'Transaksi Kas Umum';
        $jenis_transaksi = 'Kas Umum'; // Default
        $metode_pembayaran = $transaksi->metode_pembayaran ?? 'N/A';

        // --- Logika Penentuan Jenis Transaksi Berdasarkan Nilai $transaksi->jenis ---
        switch ($transaksi->jenis) {
            case 'penjualan barang':
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
                $jenis_transaksi = 'Penjualan Pulsa';
                $metode_pembayaran = $transaksi->metode_pembayaran ?? 'Cash/Transfer';
                break;
            case 'hutang barang':
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
                $jenis_transaksi = 'Piutang Pulsa';
                $metode_pembayaran = 'Piutang';
                break;
            case 'masuk':
                if ($transaksi->hutang) {
                    $detail = 'Pelunasan Hutang (ID Hutang: ' . $transaksi->hutang->id . ')';
                    $jenis_transaksi = 'Pelunasan Piutang';
                    $metode_pembayaran = 'Pelunasan';
                } else {
                    $jenis_transaksi = 'Kas Masuk';
                    $metode_pembayaran = $transaksi->metode_pembayaran ?? 'Cash';
                }
                break;
            case 'keluar':
                $jenis_transaksi = 'Kas Keluar';
                break;
            case 'expayet':
            case 'hilang':
                $jenis_transaksi = 'Kerugian Stok';
                break;
        }

        // --- Penyesuaian Nilai Total (Tanda Positif/Negatif) ---
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
