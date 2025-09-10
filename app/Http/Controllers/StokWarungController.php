<?php

namespace App\Http\Controllers;

use App\Models\StokWarung;
use App\Models\Warung;
use App\Models\Barang;
use Illuminate\Http\Request;

class StokWarungController extends Controller
{
    public function index()
    {
        $stokWarung = StokWarung::with(['warung', 'barang'])->get();
        return view('stokwarung.index', compact('stokWarung'));
    }

    public function create()
    {
        $warung = Warung::all();
        $barang = Barang::all();
        return view('stokwarung.create', compact('warung', 'barang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'id_barang' => 'required|exists:barang,id',
            'keterangan' => 'nullable|string',
        ]);

        StokWarung::create($request->all());
        return redirect()->route('stokwarung.index')->with('success', 'Stok Warung berhasil ditambahkan.');
    }

    public function show(StokWarung $stokwarung)
    {
        return view('stokwarung.show', compact('stokwarung'));
    }

    public function edit(StokWarung $stokwarung)
    {
        $warung = Warung::all();
        $barang = Barang::all();
        return view('stokwarung.edit', compact('stokwarung', 'warung', 'barang'));
    }

    public function update(Request $request, StokWarung $stokwarung)
    {
        $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'id_barang' => 'required|exists:barang,id',
            'keterangan' => 'nullable|string',
        ]);

        $stokwarung->update($request->all());
        return redirect()->route('stokwarung.index')->with('success', 'Stok Warung berhasil diperbarui.');
    }

    public function destroy(StokWarung $stokwarung)
    {
        $stokwarung->delete();
        return redirect()->route('stokwarung.index')->with('success', 'Stok Warung berhasil dihapus.');
    }
}
