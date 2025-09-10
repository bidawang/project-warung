<?php

namespace App\Http\Controllers;

use App\Models\Bunga;
use App\Models\Hutang;
use Illuminate\Http\Request;

class BungaController extends Controller
{
    public function index()
    {
        $bunga = Bunga::with('hutang')->get();
        return view('bunga.index', compact('bunga'));
    }

    public function create()
    {
        $hutang = Hutang::all();
        return view('bunga.create', compact('hutang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_hutang' => 'required|exists:hutang,id',
            'jumlah_bunga' => 'required|numeric|min:1',
        ]);

        Bunga::create($request->all());

        return redirect()->route('bunga.index')->with('success', 'Data bunga berhasil ditambahkan.');
    }

    public function show(Bunga $bunga)
    {
        $bunga->load('hutang');
        return view('bunga.show', compact('bunga'));
    }

    public function edit(Bunga $bunga)
    {
        $hutang = Hutang::all();
        return view('bunga.edit', compact('bunga', 'hutang'));
    }

    public function update(Request $request, Bunga $bunga)
    {
        $request->validate([
            'id_hutang' => 'required|exists:hutang,id',
            'jumlah_bunga' => 'required|numeric|min:1',
        ]);

        $bunga->update($request->all());

        return redirect()->route('bunga.index')->with('success', 'Data bunga berhasil diperbarui.');
    }

    public function destroy(Bunga $bunga)
    {
        $bunga->delete();
        return redirect()->route('bunga.index')->with('success', 'Data bunga berhasil dihapus.');
    }
}
