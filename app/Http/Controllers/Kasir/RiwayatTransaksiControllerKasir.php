<?php

namespace App\Http\Controllers\Kasir; // Sesuaikan dengan namespace Controller Anda
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\TransaksiKas;
use App\Models\TransaksiPulsa;
use App\Models\KasWarung; // Asumsi model ini ada
use App\Models\BarangKeluar; // Asumsi model ini ada
use Carbon\Carbon;

class RiwayatTransaksiControllerKasir extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $search = $request->get('search');
        $perPage = 10;
        $page = $request->get('page', 1);

        // 1. Ambil ID KasWarung yang terkait dengan User yang sedang login.
        // ASUMSI: Relasi Warung (id_user) -> KasWarung (id_warung) ada.
        $kasWarungIds = KasWarung::whereHas('warung', function ($query) use ($userId) {
            $query->where('id_user', $userId);
        })->pluck('id');

        if ($kasWarungIds->isEmpty()) {
            $riwayatTransaksi = new LengthAwarePaginator(collect([]), 0, $perPage, $page);
            return view('kasir.riwayat_transaksi.index', compact('riwayatTransaksi', 'search'));
        }

        // 2. Ambil TransaksiKas (Penjualan Barang, Pelunasan Hutang, Kas Masuk/Keluar)
        $transaksiKasQuery = TransaksiKas::with([
            'transaksiBarangKeluar.barangKeluar.stokWarung.barang',
            'hutang',
        ])
            ->whereIn('id_kas_warung', $kasWarungIds);

        // Filter pencarian TransaksiKas
        if ($search) {
            $transaksiKasQuery->where(function ($query) use ($search) {
                $query->where('keterangan', 'like', "%$search%")
                    ->orWhere('metode_pembayaran', 'like', "%$search%")
                    ->orWhereHas('transaksiBarangKeluar.barangKeluar.stokWarung.barang', function ($q) use ($search) {
                        $q->where('nama_barang', 'like', "%$search%");
                    });
            });
        }
        $transaksiKas = $transaksiKasQuery->get();


        // 3. Ambil TransaksiPulsa (Penjualan Pulsa/Data)
        $transaksiPulsaQuery = TransaksiPulsa::with(['pulsa'])
            ->whereIn('id_kas_warung', $kasWarungIds);

        // Filter pencarian TransaksiPulsa
        if ($search) {
            $transaksiPulsaQuery->where(function ($query) use ($search) {
                $query->whereHas('pulsa', function ($q) use ($search) {
                    $q->where('nama_pulsa', 'like', "%$search%"); // Asumsi ada kolom nama_pulsa di model Pulsa
                })
                    ->orWhere('jenis_pembayran', 'like', "%$search%");
            });
        }
        $transaksiPulsa = $transaksiPulsaQuery->get();


        // 4. Transformasi dan Gabungkan Data ke Format Standar
        $riwayatKas = $transaksiKas->map(function ($transaksi) {
            $detail = $transaksi->keterangan ?? 'Transaksi Kas Umum';
            $jenis_transaksi = 'Kas ' . ucfirst($transaksi->jenis); // Masuk/Keluar/Hutang, dll.

            // PRIORITY 1: Check for Debt/Hutang
            if ($transaksi->hutang) {
                // Transaksi ini adalah transaksi yang terhubung langsung dengan Hutang (biasanya Pelunasan atau Pencatatan Hutang Baru)
                $status_hutang = optional($transaksi->hutang)->status ?? 'Tidak Diketahui';

                // Logika untuk menentukan apakah ini Pelunasan atau Pencatatan Awal Hutang
                if ($transaksi->jenis === 'hutang') { // Asumsi jenis 'hutang' pada TransaksiKas menandakan pencatatan hutang awal/pencairan
                    $detail = 'Pencatatan Hutang Baru (Status: ' . $status_hutang . ')';
                    $jenis_transaksi = 'Pencatatan Hutang';
                } elseif ($transaksi->jenis === 'masuk') {
                    $detail = 'Pelunasan Hutang (Status: ' . $status_hutang . ')';
                    $jenis_transaksi = 'Penerimaan Pelunasan Hutang';
                } else {
                    $detail = 'Transaksi Hutang Terkait (Status: ' . $status_hutang . ')';
                    $jenis_transaksi = 'Transaksi Terkait Hutang';
                }

                // PRIORITY 2: Check for Sales/Barang Keluar
            } elseif ($transaksi->transaksiBarangKeluar->isNotEmpty()) {
                $barang = optional(optional(optional($transaksi->transaksiBarangKeluar->first())->barangKeluar)->stokWarung)->barang;
                $nama_barang = optional($barang)->nama_barang ?? 'Barang Tidak Dikenal';
                $detail = "Penjualan Barang: $nama_barang" . (count($transaksi->transaksiBarangKeluar) > 1 ? ' (+ ' . (count($transaksi->transaksiBarangKeluar) - 1) . ' item)' : '');
                $jenis_transaksi = 'Penjualan Barang';
            }

            // PRIORITY 3: General Cash Transaction
            // Jika tidak ada hutang atau barang keluar, gunakan detail default

            return (object) [
                'id_ref' => 'TK-' . $transaksi->id,
                'tanggal' => $transaksi->created_at,
                'jenis_transaksi' => $jenis_transaksi,
                'deskripsi' => $detail,
                'total' => $transaksi->total,
                'metode_pembayaran' => $transaksi->metode_pembayaran,
                'tipe_sumber' => 'TransaksiKas'
            ];
        });

        $riwayatPulsa = $transaksiPulsa->map(function ($transaksi) {
            // Transaksi Pulsa (diasumsikan sebagai penjualan)
            $nama_pulsa = optional($transaksi->pulsa)->saldo ?? 'Pulsa/Data';

            return (object) [
                'id_ref' => 'TP-' . $transaksi->id,
                'tanggal' => $transaksi->created_at,
                'jenis_transaksi' => 'Penjualan asu ' . ucfirst($transaksi->tipe), // Asumsi tipe: Pulsa/Data
                'deskripsi' => "Transaksi Pulsa: $nama_pulsa",
                'total' => $transaksi->total,
                'metode_pembayaran' => $transaksi->jenis_pembayran,
                'tipe_sumber' => 'TransaksiPulsa'
            ];
        });

        $allTransactions = $riwayatKas->merge($riwayatPulsa)->sortByDesc('tanggal');

        // 5. Pagination Manual (untuk Collection yang telah digabungkan)
        $offset = ($page * $perPage) - $perPage;

        $riwayatTransaksi = new LengthAwarePaginator(
            $allTransactions->slice($offset, $perPage)->values(),
            $allTransactions->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        // dd($riwayatTransaksi);
        // Kirim data ke view
        return view('kasir.riwayat_transaksi.index', compact('riwayatTransaksi', 'search'));
    }
}
