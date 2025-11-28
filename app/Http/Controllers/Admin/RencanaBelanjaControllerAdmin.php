<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RencanaBelanja;
use App\Models\TransaksiBarang;
use App\Models\Warung;
use App\Models\Barang;
use App\Models\AreaPembelian;
use App\Models\TransaksiAwal;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RencanaBelanjaControllerAdmin extends Controller
{
    protected function getStockData()
{
    $allTransactions = TransaksiBarang::with(['barang','areaPembelian'])
        ->whereColumn('jumlah','>','jumlah_terpakai')
        ->get()
        ->map(function($trx){
            return [
                'id'        => $trx->id,
                'id_barang' => $trx->id_barang,
                'nama_barang'=> $trx->barang->nama_barang ?? '-',
                'jumlah'    => $trx->jumlah - $trx->jumlah_terpakai, // stok real
                'harga'     => $trx->harga,
                'area'      => $trx->areaPembelian->area ?? '-',
            ];
        });

    return [
        'stockByBarang'     => $allTransactions->groupBy('id_barang'),
        'allTransactions'   => $allTransactions->values(),   // penting
        'warungs'           => Warung::all(),
        'areaPembelians'    => AreaPembelian::all()
    ];
}

public function index()
{
    $data = $this->getStockData();
    
    $rencana = RencanaBelanja::with(['barang','warung'])
    ->where('status','dibeli')
    ->get()
    ->groupBy('id_warung');
// dd($rencana);
    return view('admin.rencanabelanja.index',[
        'rencanaBelanjaByWarung' => $rencana,
        'allTransactionsForJs'   => $data['allTransactions'], // dipakai JS
        'stockByBarang'          => $data['stockByBarang'],
        'warungs'=>$data['warungs'],
    ]);
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
// dd($totalKebutuhan);
        return view('admin.rencanabelanja.pembelian', compact('totalKebutuhan'));
    }

    public function store(Request $request)
{
    // Validasi tetap sama
    $validated = $request->validate([
        'items' => 'required|array',
        'items.*.id_barang'       => 'required|integer|exists:barang,id',
        'items.*.rencana_ids'     => 'required|string',

        'items.*.purchases'       => 'required_without:items.*.skip|array',
        'items.*.purchases.*.area_pembelian_id' => 'required_without:items.*.skip|integer|exists:area_pembelian,id',
        'items.*.purchases.*.jumlah_beli'       => 'required_without:items.*.skip|integer|min:1',
        'items.*.purchases.*.harga'             => 'required_without:items.*.skip|numeric|min:0',
        'items.*.purchases.*.tanggal_kadaluarsa' => 'nullable|date',
    ]);

    $grandTotal = 0;
    $keteranganTransaksi = "Pembelian Berdasarkan Rencana Belanja Warung";
    $rencanaUpdates = [];

    DB::beginTransaction();
    try {

        $transaksi = TransaksiAwal::create([
            'tanggal' => now(),
            'keterangan' => $keteranganTransaksi,
            'total' => 0
        ]);

        foreach ($request->items as $itemData) {

            /** SKIP barang — lewati total proses */
            if (!empty($itemData['skip']) && $itemData['skip'] == 1) {
                continue;
            }

            $idBarang = $itemData['id_barang'];
            $rencanaIds = array_map('intval', explode(',', $itemData['rencana_ids']));
            $totalDibeliPerGroup = 0;

            /** Purchases bisa kosong → gunakan null-safe */
            foreach ($itemData['purchases'] ?? [] as $purchase) {

                $jumlahBeli = (int) $purchase['jumlah_beli'];
                $hargaUnit  = (float) $purchase['harga'];
                $totalHargaBaris = $jumlahBeli * $hargaUnit;

                TransaksiBarang::create([
                    'id_transaksi_awal' => $transaksi->id,
                    'id_area_pembelian' => $purchase['area_pembelian_id'],
                    'id_barang'         => $idBarang,
                    'jumlah'            => $jumlahBeli,
                    'jumlah_terpakai'    => 0,
                    'harga'             => $hargaUnit,
                    'tanggal_kadaluarsa'=> $purchase['tanggal_kadaluarsa'] ?? null,
                    'jenis'             => 'rencana',
                ]);

                $grandTotal += $totalHargaBaris;
                $totalDibeliPerGroup += $jumlahBeli;
            }

            /** update semua rencana yang terkait */
            foreach ($rencanaIds as $rencanaId) {
                $rencanaUpdates[$rencanaId] = ($rencanaUpdates[$rencanaId] ?? 0) + $totalDibeliPerGroup;
            }
        }

        foreach ($rencanaUpdates as $rencanaId => $jumlahBeli) {
            $r = RencanaBelanja::find($rencanaId);
            if ($r) {
                $r->jumlah_dibeli += $jumlahBeli;
                $r->status = 'dibeli';
                $r->save();
            }
        }

        $transaksi->update(['total' => $grandTotal]);

        DB::table('dana_utama')->where('jenis_dana','wrb_old')->decrement('saldo',$grandTotal);

        DB::commit();
        return redirect()->route('admin.rencana.index')->with('success','Transaksi rencana berhasil diproses.');
    
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error($e);
        return back()->withErrors(['process_error'=>"Gagal memproses transaksi. Error: ".$e->getMessage()]);
    }
}

}
