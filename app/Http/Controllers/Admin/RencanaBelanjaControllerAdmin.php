<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RencanaBelanja;
use App\Models\TransaksiBarang;
use App\Models\Warung;
use App\Models\Barang;
use App\Models\AreaPembelian;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RencanaBelanjaControllerAdmin extends Controller
{
    protected function getStockData()
    {
        // Mengambil semua TransaksiBarang (Stok Sumber) yang masih memiliki stok > 0
        $allTransactions = TransaksiBarang::with(['barang', 'areaPembelian']) 
        ->where('jumlah', '>', 0) 
        ->get()
        ->map(function ($trx) {
            return [
                'id'              => $trx->id,
                'id_barang'       => $trx->id_barang,
                'nama_barang'     => $trx->barang->nama_barang ?? '-',
                'jumlah'          => $trx->jumlah,
                'harga'           => $trx->harga,
                // TAMBAHKAN DATA AREA PEMBELIAN
                'area_pembelian'  => $trx->areaPembelian->area ?? null,
            ];
        });
// dd($allTransactions);
        // Group by id_barang -> list stok sumber
        $stockByBarang = $allTransactions->groupBy('id_barang')->map(function ($items) {
            return $items->map(fn($i) => [
                'id'            => $i['id'],
                'jumlah_awal'   => $i['jumlah'],
                'harga'         => $i['harga'],
            ])->values();
        });

        // Ambil semua AreaPembelian
        $areaPembelians = AreaPembelian::all();

        return [
            'stockByBarang'     => $stockByBarang,
            'warungs'           => Warung::all(),
            'allTransactions'   => $allTransactions, // Digunakan oleh JS
            'areaPembelians'    => $areaPembelians, // Data Area Pembelian baru
        ];
    }

    /**
     * Menampilkan halaman rencana belanja.
     */
    public function index(Request $request)
    {
        $data = $this->getStockData();
        $allTransactionsForJs = $data['allTransactions'];

        // Ambil Rencana Belanja yang belum selesai (jumlah_dibeli < jumlah_awal)
        $rencanaBelanjas = RencanaBelanja::with(['barang', 'warung'])
            ->whereColumn('jumlah_dibeli', '<', 'jumlah_awal')
            ->get();

        // Grouping berdasarkan ID Warung
        $rencanaBelanjaByWarung = $rencanaBelanjas->groupBy('id_warung');
// dd($rencanaBelanjaByWarung, $allTransactionsForJs, $data);
        return view('admin.rencanabelanja.index', array_merge($data, [
            'rencanaBelanjaByWarung' => $rencanaBelanjaByWarung,
            'allTransactionsForJs' => $allTransactionsForJs,
        ]));
    }

    /**
     * Memproses pengiriman rencana belanja ke warung.
     * Mengalokasikan stok sumber dan memperbarui RencanaBelanja.
     * Route: POST admin.transaksibarang.kirim.rencana.proses
     */
    

    public function create()
    {
        // 1. Ambil semua Rencana Belanja yang belum selesai
        $rencanaBelanjas = RencanaBelanja::with(['barang', 'warung'])
            ->whereColumn('jumlah_dibeli', '<', 'jumlah_awal')
            ->get();

        $totalKebutuhan = $rencanaBelanjas
            ->groupBy('id_barang') // Kelompokkan berdasarkan Barang
            ->map(function ($groupedItems, $id_barang) {

                $detailKebutuhanWarung = [];
                $totalQty = 0;
                $allRencanaIds = [];

                foreach ($groupedItems as $rencana) {
                    $kebutuhan = $rencana->jumlah_awal - $rencana->jumlah_dibeli;

                    if ($kebutuhan > 0) {
                        $totalQty += $kebutuhan;
                        $allRencanaIds[] = $rencana->id;

                        $warungName = $rencana->warung->nama_warung ?? 'Warung Tidak Dikenal';

                        $detailKebutuhanWarung[] = [
                            'warung' => $warungName,
                            'kebutuhan' => $kebutuhan,
                        ];
                    }
                }

                if ($totalQty == 0) return null;

                // **Perubahan 1: Ambil Area Pembelian yang Valid**
                $barang = Barang::find($id_barang);
                $validAreaPembelian = $barang->areaPembelian()->get(['area_pembelian.id', 'area_pembelian.area']);

                return [
                    'id_barang' => $id_barang,
                    'nama_barang' => $barang->nama_barang,
                    'total_kebutuhan' => $totalQty,
                    'rencana_ids' => array_unique($allRencanaIds),
                    'detail_warung' => $detailKebutuhanWarung,
                    // Tambahan: Area pembelian yang diperbolehkan untuk barang ini
                    'valid_areas' => $validAreaPembelian,
                ];
            })
            ->filter()
            ->values()
            ->sortBy('nama_barang');

        return view('admin.rencanabelanja.pembelian', compact('totalKebutuhan'));
    }

    public function store(Request $request)
    {
        dd($request->all());
    }
}
