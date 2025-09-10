<?php

namespace App\Http\Controllers;

use App\Models\TransaksiBarang;
use App\Models\TransaksiKas;
use App\Models\Barang;
use Illuminate\Http\Request;

class TransaksiBarangController extends Controller
{
    public function index()
    {
        $transaksibarangs = TransaksiBarang::with(['transaksiKas', 'barang'])->paginate(10);
        return view('transaksibarang.index', compact('transaksibarangs'));
    }

    public function create()
    {
        $transaksis = TransaksiKas::all();
        $barangs = Barang::all();
        return view('transaksibarang.create', compact('transaksis', 'barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_transaksi_kas' => 'required|exists:transaksi_kas,id',
            'id_barang' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'status' => 'required|string',
            'jenis' => 'required|string|in:masuk,keluar',
            'keterangan' => 'nullable|string'
        ]);

        TransaksiBarang::create($request->all());

        return redirect()->route('transaksibarang.index')->with('success', 'Transaksi barang berhasil ditambahkan.');
    }

    public function show(TransaksiBarang $transaksibarang)
    {
        return view('transaksibarang.show', compact('transaksibarang'));
    }

    public function edit(TransaksiBarang $transaksibarang)
    {
        $transaksis = TransaksiKas::all();
        $barangs = Barang::all();
        return view('transaksibarang.edit', compact('transaksibarang', 'transaksis', 'barangs'));
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
