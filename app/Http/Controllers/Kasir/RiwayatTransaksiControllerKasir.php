<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\TransaksiKas;
use App\Models\KasWarung;

class RiwayatTransaksiControllerKasir extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $search = $request->get('search');
        $perPage = 10;

        // Filter Periode (Bulan & Tahun)
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear  = $request->get('year', date('Y'));

        // Logika Tanggal 7 ke Tanggal 6 bulan berikutnya
        $startDate = \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 7)->startOfDay();
        $endDate   = $startDate->copy()->addMonth()->subDay()->endOfDay();

        $kasWarungIds = KasWarung::whereHas('warung', fn($q) => $q->where('id_user', $userId))->pluck('id');

        if ($kasWarungIds->isEmpty()) {
            $riwayatTransaksi = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, 1);
            return view('kasir.riwayat_transaksi.index', compact('riwayatTransaksi', 'search'));
        }

        $query = TransaksiKas::with([
            'transaksiBarangKeluar.barangKeluar.stokWarung.barang',
            'transaksiPulsaKeluar.pulsa',
            'hutang',
            'uangPelanggan',
        ])
            ->whereIn('id_kas_warung', $kasWarungIds)
            ->whereBetween('created_at', [$startDate, $endDate]) // Filter Periode
            ->whereNotIn('jenis', ['opname +', 'opname -'])
            ->latest();

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate($perPage);

        // Transform Data
        $riwayatTransaksi = $paginator->through(fn($trx) => $this->transformTransaksi($trx));

        // Hitung TOTAL LABA untuk periode yang dipilih (Tanpa Pagination)
        $totalLabaPeriode = 0;
        // Inisialisasi variabel statistik
        $totalLabaBarang = 0;
        $totalLabaPulsa = 0;
        $totalOmsetBarang = 0;
        $totalOmsetPulsa = 0;

        $allTrxPeriode = TransaksiKas::with(['transaksiBarangKeluar.barangKeluar', 'transaksiPulsaKeluar'])
            ->whereIn('id_kas_warung', $kasWarungIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('jenis', ['opname +', 'opname -'])
            ->get();

        foreach ($allTrxPeriode as $t) {
            // Hitung Barang
            if ($t->jenis == 'penjualan barang' || $t->jenis == 'hutang barang') {
                $totalLabaBarang += $t->transaksiBarangKeluar->sum(fn($i) => $i->barangKeluar->laba_warung ?? 0);
                $totalOmsetBarang += $t->total;
            }

            // Hitung Pulsa
            if ($t->jenis == 'penjualan pulsa' || $t->jenis == 'hutang pulsa') {
                $totalLabaPulsa += $t->transaksiPulsaKeluar->sum('laba_warung');
                $totalOmsetPulsa += $t->total;
            }
        }

        $totalOmsetPeriode = $totalOmsetBarang + $totalOmsetPulsa;
        $totalLabaPeriode = $totalLabaBarang + $totalLabaPulsa;

        return view('kasir.riwayat_transaksi.index', compact(
            'riwayatTransaksi',
            'search',
            'totalLabaPeriode',
            'totalLabaBarang',
            'totalLabaPulsa',
            'totalOmsetPeriode',
            'totalOmsetBarang',
            'totalOmsetPulsa',
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate'
        ));
    }

    protected function transformTransaksi(TransaksiKas $trx)
    {
        // =========================
        // LABEL DEFAULT
        // =========================
        $jenisLabel = 'Kas Umum';
        $metode     = $trx->metode_pembayaran ?? 'N/A';
        $deskripsi  = $trx->keterangan ?? '-';
        $totalLaba  = 0;
        $items      = [];

        // =========================
        // JENIS TRANSAKSI & LABEL (DIPERBARUI)
        // =========================
        switch ($trx->jenis) {
            case 'penjualan barang':
                $jenisLabel = 'Penjualan Barang';
                $metode     = $trx->metode_pembayaran ?? 'Cash';
                break;
            case 'hutang barang':
                $jenisLabel = 'Piutang Barang';
                $metode     = 'Piutang';
                break;
            case 'penjualan pulsa': // Untuk Cash
                $jenisLabel = 'Penjualan Pulsa';
                $metode     = 'Cash';
                break;
            case 'hutang pulsa':    // Tambahan sesuai storeJualPulsa
                $jenisLabel = 'Piutang Pulsa';
                $metode     = 'Piutang';
                break;
            case 'masuk':
                $jenisLabel = $trx->hutang ? 'Pelunasan Piutang' : 'Kas Masuk';
                $metode     = $trx->hutang ? 'Pelunasan' : $metode;
                break;
            case 'keluar':
                $jenisLabel = 'Kas Keluar';
                break;
            case 'expayet':
            case 'hilang':
                $jenisLabel = 'Kerugian Stok';
                break;
        }

        // =========================
        // PROSES BARANG KELUAR
        // =========================
        foreach ($trx->transaksiBarangKeluar as $tbk) {
            $bk     = $tbk->barangKeluar;
            $barang = optional(optional($bk->stokWarung)->barang);

            $qty   = $bk->jumlah ?? 1;
            $harga = $bk->harga_jual ?? 0;

            $totalLaba += ($bk->laba_warung ?? 0);

            $items[] = (object) [
                'nama_barang' => $barang->nama_barang ?? '-',
                'jumlah'      => $qty,
                'harga'       => $harga,
                'subtotal'    => $qty * $harga,
            ];
        }

        // =========================
        // PROSES PULSA KELUAR
        // =========================
        if ($trx->transaksiPulsaKeluar && $trx->transaksiPulsaKeluar->count() > 0) {
            foreach ($trx->transaksiPulsaKeluar as $tpk) {
                // Mengambil laba_warung sesuai yang disimpan di storeJualPulsa
                $totalLaba += ($tpk->laba_warung ?? 0);

                // Ambil operator dari relasi saldo_pulsa -> jenis_pulsa (jika ada)
                $operator = $tpk->pulsa && $tpk->pulsa->jenis ? $tpk->pulsa->jenis->nama_jenis : 'Pulsa';

                $items[] = (object) [
                    'nama_barang' => $operator . " (" . $tpk->jumlah_pulsa . ")",
                    'jumlah'      => 1,
                    'harga'       => $tpk->total,
                    'subtotal'    => $tpk->total,
                ];
            }
        }

        // =========================
        // TOTAL (+ / -) & DESKRIPSI
        // =========================
        $isKeluar = in_array($trx->jenis, ['keluar', 'expayet', 'hilang']);
        $total    = $isKeluar ? -$trx->total : $trx->total;

        // Jika keterangan kosong, buat deskripsi otomatis dari item
        if ((empty($trx->keterangan) || $trx->keterangan == '-') && count($items)) {
            $namaItem = collect($items)->pluck('nama_barang')->implode(', ');
            $deskripsi = "{$jenisLabel}: {$namaItem}";
        }

        // =========================
        // OUTPUT FINAL
        // =========================
        return (object) [
            'id'                => $trx->id,
            'id_ref'            => 'TK-' . $trx->id,
            'tanggal'           => $trx->created_at,
            'jenis_transaksi'   => $jenisLabel,
            'deskripsi'         => $deskripsi,
            'items'             => $items,
            'laba_kasir'        => (float) $totalLaba, // Pastikan numerik untuk Blade
            'total'             => (float) $total,
            'uang_dibayar'      => optional($trx->uangPelanggan)->uang_dibayar,
            'uang_kembalian'    => optional($trx->uangPelanggan)->uang_kembalian,
            'metode_pembayaran' => $metode,
            'tipe_sumber'       => 'TransaksiKas',
        ];
    }
}
