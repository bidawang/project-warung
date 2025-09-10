<?php

namespace App\Http\Controllers;

use App\Models\PembayaranHutang;
use App\Models\Hutang;
use App\Models\TransaksiKas;
use Illuminate\Http\Request;

class PembayaranHutangController extends Controller
{
    public function index()
    {
        $pembayaran = PembayaranHutang::with(['hutang', 'transaksiKas'])->get();
        return view('pembayaranhutang.index', compact('pembayaran'));
    }

    public function create()
    {
        $hutang = Hutang::all();
        $transaksiKas = TransaksiKas::all();
        return view('pembayaranhutang.create', compact('hutang', 'transaksiKas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_transaksi_kas' => 'required|exists:transaksi_kas,id',
            'id_hutang' => 'required|exists:hutang,id',
            'keterangan' => 'nullable|string',
        ]);

        PembayaranHutang::create($request->all());

        return redirect()->route('pembayaranhutang.index')->with('success', 'Pembayaran hutang berhasil ditambahkan');
    }

    public function edit($id)
    {
        $pembayaran = PembayaranHutang::findOrFail($id);
        $hutang = Hutang::all();
        $transaksiKas = TransaksiKas::all();
        return view('pembayaranhutang.edit', compact('pembayaran', 'hutang', 'transaksiKas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_transaksi_kas' => 'required|exists:transaksi_kas,id',
            'id_hutang' => 'required|exists:hutang,id',
            'keterangan' => 'nullable|string',
        ]);

        $pembayaran = PembayaranHutang::findOrFail($id);
        $pembayaran->update($request->all());

        return redirect()->route('pembayaranhutang.index')->with('success', 'Pembayaran hutang berhasil diperbarui');
    }

    public function destroy($id)
    {
        $pembayaran = PembayaranHutang::findOrFail($id);
        $pembayaran->delete();

        return redirect()->route('pembayaranhutang.index')->with('success', 'Pembayaran hutang berhasil dihapus');
    }
}
