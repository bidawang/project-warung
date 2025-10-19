<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HargaPulsa;

class HargaPulsaControllerAdmin extends Controller
{
    public function index()
    {
        $hargaPulsas = HargaPulsa::orderBy('jumlah_pulsa', 'asc')->get();
        return view('admin.harga_pulsa.index', compact('hargaPulsas'));
    }

    public function create()
    {
        return view('admin.harga_pulsa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jumlah_pulsa' => 'required|integer|min:1000',
            'harga' => 'required|integer|min:1000',
        ]);

        HargaPulsa::create([
            'jumlah_pulsa' => $request->jumlah_pulsa,
            'harga' => $request->harga,
        ]);

        return redirect()->route('admin.harga-pulsa.index')->with('success', 'Harga pulsa berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $hargaPulsa = HargaPulsa::findOrFail($id);
        $hargaPulsa->delete();

        return redirect()->route('admin.harga-pulsa.index')->with('success', 'Harga pulsa berhasil dihapus.');
    }

    public function edit($id)
    {
        $hargaPulsa = HargaPulsa::findOrFail($id);
        return view('admin.harga_pulsa.edit', compact('hargaPulsa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_pulsa' => 'required|integer|min:1000',
            'harga' => 'required|integer|min:1000',
        ]);

        $hargaPulsa = HargaPulsa::findOrFail($id);
        $hargaPulsa->update([
            'jumlah_pulsa' => $request->jumlah_pulsa,
            'harga' => $request->harga,
        ]);

        return redirect()->route('admin.harga-pulsa.index')->with('success', 'Harga pulsa berhasil diperbarui.');
    }
}
