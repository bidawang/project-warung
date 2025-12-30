<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Satuan;
use App\Models\SatuanBarang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SatuanBarangControllerAdmin extends Controller
{
    public function index()
    {
        $barang = Barang::with(['subKategori.kategori', 'satuan'])->get();
        $kategoris = \App\Models\Kategori::all();
        $subkategoris = \App\Models\SubKategori::all(); // Tambahkan ini
        $list_satuan = Satuan::orderBy('nama_satuan', 'asc')->get();

        return view('admin.satuanbarang.index', compact('barang', 'list_satuan', 'kategoris', 'subkategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:barang,id',
            'id_satuan' => [
                'required',
                'exists:satuan,id',
                // Validasi: 1 barang tidak boleh memiliki id_satuan yang sama
                Rule::unique('satuan_barang')->where(function ($query) use ($request) {
                    return $query->where('id_barang', $request->id_barang)
                        ->where('id_satuan', $request->id_satuan);
                }),
            ],
        ], [
            'id_satuan.unique' => 'Barang ini sudah memiliki satuan tersebut.'
        ]);
        // dd($cek);
        SatuanBarang::create($request->all());

        return redirect()->back()->with('success', 'Satuan berhasil ditambahkan ke barang');
    }

    public function destroy($id)
    {
        $sb = SatuanBarang::findOrFail($id);
        $sb->delete();
        return redirect()->back()->with('success', 'Relasi satuan barang dihapus');
    }

    // Opsi tambahan: Melihat detail satuan per 1 barang
    public function show($id_barang)
    {
        $barang = Barang::with('subKategori')->findOrFail($id_barang);
        $satuan_terpasang = SatuanBarang::where('id_barang', $id_barang)->with('satuan')->get();
        $list_satuan = Satuan::all();

        return view('admin.satuanbarang.show', compact('barang', 'satuan_terpasang', 'list_satuan'));
    }
}
