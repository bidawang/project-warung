<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\TransaksiBarang;
use App\Models\TransaksiKas;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\AreaPembelian;
use App\Models\Warung;
use App\Models\StokWarung;
use App\Models\TransaksiAwal;
use App\Models\TransaksiLainLain;
use Illuminate\Http\Request;

class TransaksiBarangController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = TransaksiBarang::with(['transaksiKas', 'barang', 'barangMasuk']);

        if (in_array($status, ['pending', 'dikirim', 'terima', 'tolak'])) {
            $query->whereHas('barangMasuk', function ($q) use ($status) {
                if ($status === 'dikirim') {
                    // Ada barang masuk, tapi belum diterima / ditolak
                    $q->whereNull('status');
                } elseif (in_array($status, ['terima', 'tolak'])) {
                    $q->where('status', $status);
                }
            }, $status === 'pending' ? '=' : '>', 0);

            if ($status === 'pending') {
                // Pending = tidak ada relasi barang masuk sama sekali
                $query->doesntHave('barangMasuk');
            }
        }

        $transaksibarangs = $query->paginate(10)->appends(['status' => $status]);

        $warungs = Warung::all();

        $stokWarungData = StokWarung::select('id_warung', 'id_barang')
            ->withSum(['barangMasuk as stok' => function ($q) {
                $q->where('status', 'terima');
            }], 'jumlah')
            ->get()
            ->map(fn($item) => [
                'id_warung' => $item->id_warung,
                'id_barang' => $item->id_barang,
                'stok' => $item->stok ?? 0,
            ]);

        return view('admin.transaksibarang.index', compact('transaksibarangs', 'status', 'warungs', 'stokWarungData'));
    }



    /**
     * Update status massal untuk transaksi barang (dari checkbox di pending)
     */
    public function updateStatusMassal(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'status' => 'required|in:terima,tolak',
        ]);

        TransaksiBarang::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return redirect()->route('transaksibarang.index', ['status' => 'pending'])
            ->with('success', 'Status transaksi berhasil diperbarui.');
    }


    public function create()
    {
        $transaksis = TransaksiKas::all();

        // Ambil semua barang
        $barangs = Barang::all();

        // Ambil semua area pembelian
        $areas = AreaPembelian::all();

        return view('admin.transaksibarang.create', compact('transaksis', 'barangs', 'areas'));
    }



    public function store(Request $request)
    {
        // Simpan transaksi awal
        $transaksi = TransaksiAwal::create([
            'tanggal' => now(),
            'keterangan' => $request->keterangan,
            'total' => 0, // akan dihitung di bawah
        ]);

        $grandTotal = 0;

        // Loop area pembelian
        if ($request->id_area) {
            foreach ($request->id_area as $areaIndex => $areaId) {
                if (isset($request->id_barang[$areaIndex])) {
                    foreach ($request->id_barang[$areaIndex] as $i => $barangId) {
                        $jumlah = $request->jumlah[$areaIndex][$i] ?? 0;
                        $harga  = $request->total_harga[$areaIndex][$i] ?? 0;

                        TransaksiBarang::create([
                            'id_transaksi_awal'   => $transaksi->id,
                            'id_area_pembelian'   => $areaId,
                            'id_barang'           => $barangId,
                            'jumlah'              => $jumlah,
                            'harga'               => $harga,
                            'jenis'               => 'masuk',
                        ]);

                        $grandTotal += ($jumlah * $harga);
                    }
                }
            }
        }

        // Loop transaksi lain-lain (opsional)
        if ($request->lain_keterangan && $request->lain_harga) {
            foreach ($request->lain_keterangan as $i => $ket) {
                $harga = $request->lain_harga[$i] ?? 0;

                // Lewati jika kosong
                if (!$ket && !$harga) {
                    continue;
                }

                TransaksiLainLain::create([
                    'id_transaksi_awal' => $transaksi->id,
                    'keterangan'       => $ket,
                    'harga'            => $harga,
                ]);

                $grandTotal += $harga;
            }
        }

        // Update total transaksi awal
        $transaksi->update(['total' => $grandTotal]);

        return redirect()->route('transaksibarang.index')
            ->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function kirimMassalProses(Request $request)
    {
        $request->validate([
            'transaksi_ids' => 'required|string',
            'warung_id' => 'required|exists:warung,id',
        ]);

        $ids = explode(',', $request->transaksi_ids);

        foreach ($ids as $id) {
            $transaksiBarang = TransaksiBarang::find($id);
            if (!$transaksiBarang) {
                continue;
            }
            // Cari stok warung untuk barang ini
            $stokWarung = StokWarung::firstOrCreate(
                [
                    'id_warung' => $request->warung_id,
                    'id_barang' => $transaksiBarang->id_barang, // pastikan pakai field yang benar
                ],
                [
                    'jumlah' => 0
                ]
            );


            // Buat barang masuk (status masih null = dikirim, belum diterima/ditolak)
            BarangMasuk::create([
                'id_transaksi_barang' => $transaksiBarang->id,
                'id_stok_warung' => $stokWarung->id,
                'id_barang' => $transaksiBarang->id_barang, // pastikan pakai field yang benar
                'jumlah' => $transaksiBarang->jumlah,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('transaksibarang.index', ['status' => 'dikirim'])
            ->with('success', 'Barang berhasil dikirim ke warung!');
    }




    public function show(TransaksiBarang $transaksibarang)
    {
        return view('admin/transaksibarang.show', compact('transaksibarang'));
    }

    public function edit(TransaksiBarang $transaksibarang)
    {
        $transaksis = TransaksiKas::all();
        $barangs = Barang::all();
        return view('admin/transaksibarang.edit', compact('transaksibarang', 'transaksis', 'barangs'));
    }

    public function update(Request $request, TransaksiBarang $transaksibarang)
    {
        $request->validate([
            'id_transaksi_kas' => 'required|exists:transaksi_kas,id',
            'id_barang' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'status' => 'required|string',
            'jenis' => 'required|string|in:masuk,keluar',
            'keterangan' => 'nullable|string'
        ]);

        $transaksibarang->update($request->all());

        return redirect()->route('transaksibarang.index')->with('success', 'Transaksi barang berhasil diperbarui.');
    }

    public function destroy(TransaksiBarang $transaksibarang)
    {
        $transaksibarang->delete();
        return redirect()->route('transaksibarang.index')->with('success', 'Transaksi barang berhasil dihapus.');
    }
}
