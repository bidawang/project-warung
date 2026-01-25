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
        $userId  = Auth::id();
        $search  = $request->get('search');
        $perPage = 10;

        // Ambil kas warung milik user
        $kasWarungIds = KasWarung::whereHas(
            'warung',
            fn($q) => $q->where('id_user', $userId)
        )->pluck('id');

        if ($kasWarungIds->isEmpty()) {
            $riwayatTransaksi = new LengthAwarePaginator([], 0, $perPage, 1);
            return view('kasir.riwayat_transaksi.index', compact('riwayatTransaksi', 'search'));
        }

        $query = TransaksiKas::with([
            'transaksiBarangKeluar.barangKeluar.stokWarung.barang',
            'hutang',
            'uangPelanggan',
        ])
            ->whereIn('id_kas_warung', $kasWarungIds)
            ->whereNotIn('jenis', ['opname +', 'opname -'])
            ->latest();

        // Pencarian
        $query->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('keterangan', 'like', "%{$search}%")
                    ->orWhere('metode_pembayaran', 'like', "%{$search}%")
                    ->orWhereHas(
                        'transaksiBarangKeluar.barangKeluar.stokWarung.barang',
                        fn($qb) => $qb->where('nama_barang', 'like', "%{$search}%")
                    );
            });
        });

        $paginator = $query->paginate($perPage);

        $riwayatTransaksi = $paginator->through(
            fn($trx) => $this->transformTransaksi($trx)
        );

        return view('kasir.riwayat_transaksi.index', compact('riwayatTransaksi', 'search'));
    }

    /**
     * Transform transaksi kas → format siap struk
     */
    protected function transformTransaksi(TransaksiKas $trx)
    {
        // =========================
        // LABEL DEFAULT
        // =========================
        $jenisLabel = 'Kas Umum';
        $metode     = $trx->metode_pembayaran ?? 'N/A';
        $deskripsi  = $trx->keterangan ?? '-';

        // =========================
        // JENIS TRANSAKSI
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

            case 'masuk':
                if ($trx->hutang) {
                    $jenisLabel = 'Pelunasan Piutang';
                    $metode     = 'Pelunasan';
                } else {
                    $jenisLabel = 'Kas Masuk';
                }
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
        // TOTAL (+ / -)
        // =========================
        $isKeluar = in_array($trx->jenis, ['keluar', 'expayet', 'hilang']);
        $total    = $isKeluar ? -$trx->total : $trx->total;

        // =========================
        // ITEMS STRUK (INTI)
        // =========================
        $items = [];

        foreach ($trx->transaksiBarangKeluar as $tbk) {
            $bk     = $tbk->barangKeluar;
            $barang = optional(optional($bk->stokWarung)->barang);

            $qty   = $bk->jumlah ?? 1;
            $harga = $bk->harga_jual ?? 0;

            $items[] = (object) [
                'nama_barang' => $barang->nama_barang ?? '-',
                'jumlah'      => $qty,
                'harga'       => $harga,
                'subtotal'    => $qty * $harga,
            ];
        }

        // =========================
        // DESKRIPSI OTOMATIS
        // =========================
        if (empty($trx->keterangan) && count($items)) {
            $namaBarang = collect($items)->pluck('nama_barang')->implode(', ');
            $deskripsi  = "{$jenisLabel}: {$namaBarang}";
        }

        // =========================
        // UANG PELANGGAN
        // =========================
        $uangDibayar   = optional($trx->uangPelanggan)->uang_dibayar;
        $uangKembalian = optional($trx->uangPelanggan)->uang_kembalian;

        // =========================
        // OUTPUT FINAL
        // =========================
        return (object) [
            'id_ref'            => 'TK-' . $trx->id,
            'tanggal'           => $trx->created_at,
            'jenis_transaksi'   => $jenisLabel,
            'deskripsi'         => $deskripsi,

            // STRUK
            'items'             => $items,

            // TOTAL
            'total'             => number_format($total, 2, '.', ''),
            'uang_dibayar'      => $uangDibayar !== null
                ? number_format($uangDibayar, 2, '.', '')
                : null,
            'uang_kembalian'    => $uangKembalian !== null
                ? number_format($uangKembalian, 2, '.', '')
                : null,

            'metode_pembayaran' => $metode,
            'tipe_sumber'       => 'TransaksiKas',
        ];
    }
}
