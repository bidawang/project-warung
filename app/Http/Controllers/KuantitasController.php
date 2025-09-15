<?php

namespace App\Http\Controllers;

use App\Models\Kuantitas;
use App\Models\StokWarung;
use Illuminate\Http\Request;

class KuantitasController extends Controller
{
    public function index()
    {
        $kuantitas = Kuantitas::with('stokWarung.warung', 'stokWarung.barang')->get();
        return view('kuantitas.index', compact('kuantitas'));
    }

    public function create(Request $request)
    {
        $selectedStokWarung = null;
        $hargaSatuan = null;

        if ($request->has('id_stok_warung')) {
            $selectedStokWarung = StokWarung::with('barang', 'warung')->findOrFail($request->id_stok_warung);

            // Ambil harga satuan dari transaksi terakhir barang
            $transaksi = $selectedStokWarung->barang->transaksiBarang()->latest()->first();

            if ($transaksi) {
                $hargaDasar = $transaksi->harga / max($transaksi->jumlah, 1);
                $markupPercent = optional($transaksi->areaPembelian)->markup ?? 0;
                $hargaSatuan   = $hargaDasar + ($hargaDasar * $markupPercent / 100);
            }
        }

        $stokWarung = StokWarung::with('barang', 'warung')->get();

        return view('kuantitas.create', compact('stokWarung', 'selectedStokWarung', 'hargaSatuan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_stok_warung' => 'required|exists:stok_warung,id',
            'jumlah' => 'required|integer|min:1',
            'harga_jual' => 'required|numeric|min:0',
        ]);
        // dd($request->all());

        Kuantitas::create($request->all());

        return redirect()->route('kuantitas.index')->with('success', 'Kuantitas berhasil ditambahkan.');
    }

    public function show(Kuantitas $kuantita)
    {
        $kuantita->load('stokWarung.barang', 'stokWarung.warung');
        return view('kuantitas.show', compact('kuantita'));
    }

    public function edit(Kuantitas $kuantita)
    {
        $stokWarung = StokWarung::with('barang', 'warung')->get();
        return view('kuantitas.edit', compact('kuantita', 'stokWarung'));
    }

    public function update(Request $request, Kuantitas $kuantita)
    {
        $request->validate([
            'id_stok_warung' => 'required|exists:stok_warung,id',
            'jumlah' => 'required|integer|min:1',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        $kuantita->update($request->all());

        return redirect()->route('kuantitas.index')->with('success', 'Kuantitas berhasil diperbarui.');
    }

    public function destroy(Kuantitas $kuantita)
    {
        $kuantita->delete();
        return redirect()->route('kuantitas.index')->with('success', 'Kuantitas berhasil dihapus.');
    }
}
