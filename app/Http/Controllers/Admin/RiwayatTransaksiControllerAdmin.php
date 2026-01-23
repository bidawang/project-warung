<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransaksiKas;
use App\Models\Warung;
use Carbon\Carbon;

class RiwayatTransaksiControllerAdmin extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $search    = $request->get('search');

        $warungs = Warung::with('kasWarung')->get();
        $dataTransaksiPerWarung = collect();

        foreach ($warungs as $warung) {
            $kasWarung = $warung->kasWarung->first();

            if (!$kasWarung) {
                $dataTransaksiPerWarung->push([
                    'id' => $warung->id,
                    'nama_warung' => $warung->nama_warung,
                    'total_kas_warung' => 'N/A',
                    'riwayat_transaksi' => collect(),
                ]);
                continue;
            }

            $query = TransaksiKas::with([
                'transaksiBarangKeluar.barangKeluar.stokWarung.barang',
                'hutang',
                'uangPelanggan',
            ])
                ->where('id_kas_warung', $kasWarung->id)
                ->latest();

            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }

            if ($endDate) {
                $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('keterangan', 'like', "%{$search}%")
                        ->orWhere('metode_pembayaran', 'like', "%{$search}%")
                        ->orWhereHas(
                            'transaksiBarangKeluar.barangKeluar.stokWarung.barang',
                            fn($qb) => $qb->where('nama_barang', 'like', "%{$search}%")
                        );
                });
            }

            $riwayatTransaksi = $query->get()->map(function ($trx) use ($warung) {
                $data = $this->transformTransaksi($trx);
                $data->id_warung   = $warung->id;
                $data->nama_warung = $warung->nama_warung;
                return $data;
            });

            $totalKasWarung = $kasWarung->total_kas
                ?? $riwayatTransaksi->sum(fn($t) => (float) $t->total);

            $dataTransaksiPerWarung->push([
                'id' => $warung->id,
                'nama_warung' => $warung->nama_warung,
                'total_kas_warung' => $totalKasWarung,
                'riwayat_transaksi' => $riwayatTransaksi,
            ]);
        }

        return view('admin.riwayat_transaksi.index', compact(
            'dataTransaksiPerWarung',
            'startDate',
            'endDate',
            'search'
        ));
    }

    /**
     * Transform transaksi (SAMA DENGAN KASIR)
     */
    protected function transformTransaksi(TransaksiKas $trx)
    {
        // LABEL DEFAULT
        $jenisLabel = 'Kas Umum';
        $metode     = $trx->metode_pembayaran ?? 'N/A';
        $deskripsi  = $trx->keterangan ?? '-';

        // JENIS TRANSAKSI
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

        // TOTAL (+ / -)
        $isKeluar = in_array($trx->jenis, ['keluar', 'expayet', 'hilang']);
        $total    = $isKeluar ? -$trx->total : $trx->total;

        // ITEMS STRUK
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

        // DESKRIPSI OTOMATIS
        if (empty($trx->keterangan) && count($items)) {
            $namaBarang = collect($items)->pluck('nama_barang')->implode(', ');
            $deskripsi  = "{$jenisLabel}: {$namaBarang}";
        }

        // UANG PELANGGAN
        $uangDibayar   = optional($trx->uangPelanggan)->uang_dibayar;
        $uangKembalian = optional($trx->uangPelanggan)->uang_kembalian;

        return (object) [
            'id_ref'            => 'TK-' . $trx->id,
            'tanggal'           => $trx->created_at,
            'jenis_transaksi'   => $jenisLabel,
            'deskripsi'         => $deskripsi,
            'items'             => $items,
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
