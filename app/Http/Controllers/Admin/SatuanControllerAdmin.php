<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanControllerAdmin extends Controller
{
    public function index()
    {
        // withCount akan menghasilkan kolom 'barang_count'
        $satuan = Satuan::withCount('barang')->get();
        return view('admin.satuan.index', compact('satuan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_satuan' => 'required|string|max:100',
            'nama_satuan'     => 'required|string|max:255|unique:satuan,nama_satuan',
            'jumlah'          => 'required|integer|min:1',
        ]);

        Satuan::create($request->all());
        return redirect()->back()->with('success', 'Satuan baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori_satuan' => 'required|string|max:100',
            'nama_satuan'     => 'required|string|max:255|unique:satuan,nama_satuan,' . $id,
            'jumlah'          => 'required|integer|min:1',
        ]);

        $satuan = Satuan::findOrFail($id);
        $satuan->update($request->all());
        return redirect()->back()->with('success', 'Data satuan diperbarui.');
    }

    public function destroy($id)
    {
        $satuan = Satuan::findOrFail($id);

        // Proteksi jika satuan masih digunakan oleh barang
        if ($satuan->barang()->exists()) {
            return redirect()->back()->with('error', 'Satuan tidak bisa dihapus karena masih digunakan oleh barang.');
        }

        $satuan->delete();
        return redirect()->back()->with('success', 'Satuan berhasil dihapus');
    }
}
