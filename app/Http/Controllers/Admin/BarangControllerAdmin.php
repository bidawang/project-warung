<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Subkategori;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BarangControllerAdmin extends Controller
{
    /**
     * Menampilkan daftar semua barang.
     */
    public function index()
    {
        $barangs = Barang::with('subKategori.kategori')->get();
        return view('admin.barang.index', compact('barangs'));
    }

    /**
     * Menampilkan formulir untuk membuat barang baru.
     */
    public function create()
    {
        $subkategoris = Subkategori::with('kategori')->get();
        return view('admin.barang.create', compact('subkategoris'));
    }

    /**
     * Menyimpan barang baru ke database.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_sub_kategori' => 'required|exists:sub_kategori,id',
                'kode_barang' => 'required|string|max:255|unique:barang,kode_barang',
                'nama_barang' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
            ]);

            Barang::create($validatedData);

            return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan barang. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menampilkan formulir untuk mengedit barang tertentu.
     */
    public function edit(Barang $barang)
    {
        $subkategoris = Subkategori::with('kategori')->get();
        return view('admin.barang.edit', compact('barang', 'subkategoris'));
    }

    /**
     * Memperbarui barang yang ada di database.
     */
    public function update(Request $request, Barang $barang)
    {
        try {
            $validatedData = $request->validate([
                'id_sub_kategori' => 'required|exists:subkategori,id',
                'kode_barang' => 'required|string|max:255|unique:barang,kode_barang,' . $barang->id,
                'nama_barang' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
            ]);

            $barang->update($validatedData);

            return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui barang. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menghapus barang dari database.
     */
    public function destroy(Barang $barang)
    {
        try {
            $barang->delete();
            return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus barang. Silakan coba lagi.');
        }
    }
}
