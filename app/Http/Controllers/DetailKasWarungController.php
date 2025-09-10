<?php

namespace App\Http\Controllers;

use App\Models\DetailKasWarung;
use App\Models\KasWarung;
use Illuminate\Http\Request;

class DetailKasWarungController extends Controller
{
    public function index()
    {
        $detailKas = DetailKasWarung::with('kasWarung.warung')->get();
        return view('detailkaswarung.index', compact('detailKas'));
    }

    public function create()
    {
        $kas = KasWarung::with('warung')->get();
        return view('detailkaswarung.create', compact('kas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kas_warung' => 'required|exists:kas_warung,id',
            'pecahan' => 'required|numeric',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        DetailKasWarung::create($request->all());

        return redirect()->route('detailkaswarung.index')->with('success', 'Detail Kas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $detailKas = DetailKasWarung::findOrFail($id);
        $kasWarungs = KasWarung::with('warung')->get();
        return view('detailkaswarung.edit', compact('detailKas', 'kasWarungs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_kas_warung' => 'required|exists:kas_warung,id',
            'pecahan' => 'required|numeric',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $detailKas = DetailKasWarung::findOrFail($id);
        $detailKas->update($request->all());

        return redirect()->route('detailkaswarung.index')->with('success', 'Detail Kas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $detailKas = DetailKasWarung::findOrFail($id);
        $detailKas->delete();

        return redirect()->route('detailkaswarung.index')->with('success', 'Detail Kas berhasil dihapus.');
    }
}
