<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaksi;
use App\Models\TransaksiKas;
use Illuminate\Http\Request;

class DetailTransaksiController extends Controller
{
    public function index()
    {
        $details = DetailTransaksi::with('transaksi')->latest()->paginate(10);
        return view('detailtransaksi.index', compact('details'));
    }

    public function create()
    {
        $transaksis = TransaksiKas::all();
        return view('detailtransaksi.create', compact('transaksis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_transaksi' => 'required|exists:transaksi_kas,id',
            'pecahan' => 'required|numeric',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        DetailTransaksi::create($validated);

        return redirect()->route('detailtransaksi.index')->with('success', 'Detail transaksi berhasil ditambahkan');
    }

    public function show(DetailTransaksi $detailtransaksi)
    {
        $detailtransaksi->load('transaksi');
        return view('detailtransaksi.show', compact('detailtransaksi'));
    }

    public function edit(DetailTransaksi $detailtransaksi)
    {
        $transaksis = TransaksiKas::all();
        return view('detailtransaksi.edit', compact('detailtransaksi', 'transaksis'));
    }

    public function update(Request $request, DetailTransaksi $detailtransaksi)
    {
        $validated = $request->validate([
            'id_transaksi' => 'required|exists:transaksi_kas,id',
            'pecahan' => 'required|numeric',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $detailtransaksi->update($validated);

        return redirect()->route('detailtransaksi.index')->with('success', 'Detail transaksi berhasil diperbarui');
    }

    public function destroy(DetailTransaksi $detailtransaksi)
    {
        $detailtransaksi->delete();
        return redirect()->route('detailtransaksi.index')->with('success', 'Detail transaksi berhasil dihapus');
    }
}
