<?php

namespace App\Http\Controllers;

use App\Models\TransaksiKas;
use App\Models\KasWarung;
use Illuminate\Http\Request;

class TransaksiKasController extends Controller
{
    public function index()
    {
        $transaksis = TransaksiKas::with('kasWarung')->latest()->paginate(10);
        return view('transaksikas.index', compact('transaksis'));
    }

    public function create()
    {
        $kasWarungs = KasWarung::all();
        return view('transaksikas.create', compact('kasWarungs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kas_warung' => 'required|exists:kas_warung,id',
            'total' => 'required|numeric',
            'metode_pembayaran' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        TransaksiKas::create($validated);

        return redirect()->route('transaksikas.index')->with('success', 'Transaksi berhasil ditambahkan');
    }

    public function show(TransaksiKas $transaksika)
    {
        $transaksika->load('kasWarung');
        return view('transaksikas.show', compact('transaksika'));
    }

    public function edit(TransaksiKas $transaksika)
    {
        $kasWarungs = KasWarung::all();
        return view('transaksikas.edit', compact('transaksika', 'kasWarungs'));
    }

    public function update(Request $request, TransaksiKas $transaksika)
    {
        $validated = $request->validate([
            'id_kas_warung' => 'required|exists:kas_warung,id',
            'total' => 'required|numeric',
            'metode_pembayaran' => 'required|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        $transaksika->update($validated);

        return redirect()->route('transaksikas.index')->with('success', 'Transaksi berhasil diperbarui');
    }

    public function destroy(TransaksiKas $transaksika)
    {
        $transaksika->delete();
        return redirect()->route('transaksikas.index')->with('success', 'Transaksi berhasil dihapus');
    }
}
