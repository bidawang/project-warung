<?php

namespace App\Http\Controllers;

use App\Models\KasWarung;
use App\Models\Warung;
use Illuminate\Http\Request;

class KasWarungController extends Controller
{
    public function index()
    {
        $kasWarung = KasWarung::with('warung')->get();
        return view('kaswarung.index', compact('kasWarung'));
    }

    public function create()
    {
        $warungs = Warung::all();
        return view('kaswarung.create', compact('warungs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'jenis_kas' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        KasWarung::create($request->all());

        return redirect()->route('kaswarung.index')->with('success', 'Kas Warung berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kasWarung = KasWarung::findOrFail($id);
        $warungs = Warung::all();
        return view('kaswarung.edit', compact('kasWarung', 'warungs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'jenis_kas' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $kasWarung = KasWarung::findOrFail($id);
        $kasWarung->update($request->all());

        return redirect()->route('kaswarung.index')->with('success', 'Kas Warung berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kasWarung = KasWarung::findOrFail($id);
        $kasWarung->delete();

        return redirect()->route('kaswarung.index')->with('success', 'Kas Warung berhasil dihapus.');
    }
    public function show($id)
    {
        $kasWarung = KasWarung::with(['warung', 'detailKasWarung'])->findOrFail($id);
        return view('kaswarung.show', compact('kasWarung'));
    }
}
