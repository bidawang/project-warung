<?php

namespace App\Http\Controllers;

use App\Models\BarangHutang;
use App\Models\Hutang;
use App\Models\TransaksiBarang;
use Illuminate\Http\Request;

class BarangHutangController extends Controller
{
    public function index()
    {
        $baranghutang = BarangHutang::with(['hutang', 'transaksiBarang'])->get();
        return view('baranghutang.index', compact('baranghutang'));
    }

    public function create()
    {
        $hutang = Hutang::all();
        $transaksiBarang = TransaksiBarang::all();
        return view('baranghutang.create', compact('hutang', 'transaksiBarang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_hutang' => 'required|exists:hutang,id',
            'id_transaksi_barang' => 'required|exists:transaksi_barang,id',
        ]);

        BarangHutang::create($request->all());

        return redirect()->route('baranghutang.index')->with('success', 'Barang hutang berhasil ditambahkan');
    }

    public function edit($id)
    {
        $baranghutang = BarangHutang::findOrFail($id);
        $hutang = Hutang::all();
        $transaksiBarang = TransaksiBarang::all();
        return view('baranghutang.edit', compact('baranghutang', 'hutang', 'transaksiBarang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_hutang' => 'required|exists:hutang,id',
            'id_transaksi_barang' => 'required|exists:transaksi_barang,id',
        ]);

        $baranghutang = BarangHutang::findOrFail($id);
        $baranghutang->update($request->all());

        return redirect()->route('baranghutang.index')->with('success', 'Barang hutang berhasil diperbarui');
    }

    public function destroy($id)
    {
        $baranghutang = BarangHutang::findOrFail($id);
        $baranghutang->delete();

        return redirect()->route('baranghutang.index')->with('success', 'Barang hutang berhasil dihapus');
    }
}
