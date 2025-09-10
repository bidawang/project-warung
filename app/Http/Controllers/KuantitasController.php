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

    public function create()
    {
        $stokWarung = StokWarung::with('barang', 'warung')->get();
        return view('kuantitas.create', compact('stokWarung'));
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
