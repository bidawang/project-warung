<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\StokWarung;
use App\Models\Laba;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barang_keluar = BarangKeluar::whereHas('stokWarung', function ($q) {
            $q->where('id_warung', session('id_warung'));
        })
            ->with(['stokWarung.barang']) // biar langsung load barangnya juga
            ->latest()
            ->get();

        return view('barangkeluar.index', compact('barang_keluar'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stok_warungs = StokWarung::where('id_warung', session('id_warung'))
            ->with(['barang.transaksiBarang.areaPembelian', 'kuantitas'])
            ->get();

        $stok_warungs->transform(function ($stok) {
            // Hitung stok saat ini
            $stokMasuk = $stok->barangMasuk()
                ->where('status', 'terima')
                ->whereHas('stokWarung', fn($q) => $q->where('id_warung', session('id_warung')))
                ->sum('jumlah');

            $stokKeluar = $stok->barangKeluar()
                ->whereHas('stokWarung', fn($q) => $q->where('id_warung', session('id_warung')))
                ->sum('jumlah');

            $mutasiMasuk = $stok->mutasiBarang()
                ->where('status', 'terima')
                ->whereHas('stokWarung', fn($q) => $q->where('id_warung', session('id_warung')))
                ->sum('jumlah');

            $mutasiKeluar = $stok->mutasiBarang()
                ->where('status', 'keluar')
                ->whereHas('stokWarung', fn($q) => $q->where('id_warung', session('id_warung')))
                ->sum('jumlah');

            $stokSaatIni = $stokMasuk + $mutasiMasuk - $mutasiKeluar - $stokKeluar;
            $stok->stok_saat_ini = $stokSaatIni;

            // Ambil transaksi terbaru
            $transaksi = $stok->barang->transaksiBarang()->latest()->first();

            if (!$transaksi) {
                $stok->harga_jual = 0;
                $stok->kuantitas_list = [];
                return $stok;
            }

            // Harga dasar = total beli / jumlah
            $hargaDasar = $transaksi->harga / max($transaksi->jumlah, 1);

            // Tambahkan markup dari area pembelian
            $markupPercent = optional($transaksi->areaPembelian)->markup ?? 0;
            $hargaSatuan = $hargaDasar + ($hargaDasar * $markupPercent / 100);

            // Ambil harga_jual dari tabel Laba
            $laba = Laba::where('input_minimal', '<=', $hargaSatuan)
                ->where('input_maksimal', '>=', $hargaSatuan)
                ->first();

            $stok->harga_jual = $laba ? $laba->harga_jual : 0;

            // Daftar kuantitas (bundle)
            $stok->kuantitas_list = $stok->kuantitas->map(fn($k) => [
                'jumlah' => $k->jumlah,
                'harga_jual' => $k->harga_jual,
            ]);

            return $stok;
        });

        return view('barangkeluar.create', compact('stok_warungs'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id_stok_warung'   => 'required|array',
            'id_stok_warung.*' => 'required|exists:stok_warung,id',
            'jumlah'           => 'required|array',
            'jumlah.*'         => 'required|integer|min:1',
            'jenis'            => 'required|array',
            'jenis.*'          => 'required|in:penjualan,hutang,expayet,hilang',
            'keterangan'       => 'nullable|string',
        ]);

        $grandTotal = 0;
        $barangKeluarList = [];
        $idWarung = session('id_warung');

        foreach ($validatedData['id_stok_warung'] as $i => $stokId) {
            $jumlah = $validatedData['jumlah'][$i];
            $stok = \App\Models\StokWarung::with([
                'barang.transaksiBarang.areaPembelian',
                'barangMasuk',
                'barangKeluar',
                'mutasiBarang'
            ])->findOrFail($stokId);

            // Buat closure untuk filter stokWarung by id_warung agar tidak duplikasi
            $filterByWarung = fn($query) => $query->whereHas('stokWarung', fn($q) => $q->where('id_warung', $idWarung));

            $stokMasuk = $stok->barangMasuk()->where('status', 'terima')->where($filterByWarung)->sum('jumlah');
            $stokKeluar = $stok->barangKeluar()->where($filterByWarung)->sum('jumlah');
            $mutasiMasuk = $stok->mutasiBarang()->where('status', 'terima')->where($filterByWarung)->sum('jumlah');
            $mutasiKeluar = $stok->mutasiBarang()->where('status', 'keluar')->where($filterByWarung)->sum('jumlah');

            $stokSaatIni = $stokMasuk + $mutasiMasuk - $mutasiKeluar - $stokKeluar;

            $transaksi = $stok->barang->transaksiBarang()->latest()->first();

            $hargaJual = 0;
            if ($transaksi) {
                $hargaDasar = $transaksi->harga / max($transaksi->jumlah, 1);
                $markupPercent = optional($transaksi->areaPembelian)->markup ?? 0;
                $hargaSatuan = $hargaDasar + ($hargaDasar * $markupPercent / 100);

                $laba = \App\Models\Laba::where('input_minimal', '<=', $hargaSatuan)
                    ->where('input_maksimal', '>=', $hargaSatuan)
                    ->first();

                $hargaJual = $laba ? $laba->harga_jual : 0;
            }

            $totalItem = $hargaJual * $jumlah;
            $grandTotal += $totalItem;

            $barangKeluarList[] = [
                'stokId'    => $stokId,
                'jumlah'    => $jumlah,
                'jenis'     => $validatedData['jenis'][$i],
                'hargaJual' => $hargaJual,
                'totalItem' => $totalItem,
            ];
        }

        // Buat 1 transaksi kas
        $transaksiKas = \App\Models\TransaksiKas::create([
            'id_kas_warung'     => $idWarung,
            // 'id_hutang'         => null,
            'total'             => $grandTotal,
            'metode_pembayaran' => $request->metode_pembayaran,
            'keterangan'        => $request->keterangan,
            'jenis'             => $validatedData['jenis'][$i],
        ]);

        // Simpan semua barang keluar + transaksi barang keluar
        foreach ($barangKeluarList as $item) {
            $barangKeluar = \App\Models\BarangKeluar::create([
                'id_stok_warung' => $item['stokId'],
                'jumlah'         => $item['jumlah'],
                'jenis'          => $item['jenis'],
                'keterangan'     => $request->keterangan,
            ]);

            \App\Models\TransaksiBarangKeluar::create([
                'id_transaksi_kas' => $transaksiKas->id,
                'id_barang_keluar' => $barangKeluar->id,
                'jumlah'           => $item['totalItem'], // total uang
            ]);
        }

        return redirect()->route('barangkeluar.index')
            ->with('success', 'Transaksi barang keluar berhasil ditambahkan!');
    }



    public function show(BarangKeluar $barangKeluar)
    {
        return view('barangkeluar.show', compact('barangKeluar'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BarangKeluar $barangKeluar)
    {
        $barangKeluar->delete();

        return redirect()->route('barangkeluar.index')
            ->with('success', 'Transaksi barang keluar berhasil dihapus!');
    }
}
