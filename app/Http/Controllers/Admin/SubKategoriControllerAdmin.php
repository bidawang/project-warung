<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subkategori;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubkategoriControllerAdmin extends Controller
{
    /**
     * Menampilkan daftar semua subkategori.
     */
    public function index()
    {
        $subkategoris = Subkategori::with('kategori')->get();
        return view('/admin/subkategori.index', compact('subkategoris'));
    }

    /**
     * Menampilkan formulir untuk membuat subkategori baru.
     */
    public function create()
    {
        $kategoris = Kategori::all();
        return view('/admin/subkategori.create', compact('kategoris'));
    }

    /**
     * Menyimpan subkategori baru ke database.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id_kategori' => 'required|exists:kategori,id',
                'sub_kategori' => 'required',
                'keterangan' => 'nullable|string',
            ]);
            Subkategori::create($validatedData);
            return redirect()->route('subkategori.index')->with('success', 'Subkategori berhasil ditambahkan!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan subkategori. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menampilkan formulir untuk mengedit subkategori tertentu.
     */
    public function edit(Subkategori $subkategori)
    {
        $kategoris = Kategori::all();
        return view('/admin/subkategori.edit', compact('subkategori', 'kategoris'));
    }

    /**
     * Memperbarui subkategori yang ada di database.
     */
    public function update(Request $request, Subkategori $subkategori)
    {
        try {
            $validatedData = $request->validate([
                'id_kategori' => 'required|exists:kategori,id',
                'subkategori' => 'required|string|max:255|unique:subkategori,subkategori,' . $subkategori->id,
                'keterangan' => 'nullable|string',
            ]);

            $subkategori->update($validatedData);

            return redirect()->route('subkategori.index')->with('success', 'Subkategori berhasil diperbarui!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui subkategori. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menghapus subkategori dari database.
     */
    public function destroy(Subkategori $subkategori)
    {
        try {
            $subkategori->delete();
            return redirect()->route('subkategori.index')->with('success', 'Subkategori berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus subkategori. Silakan coba lagi.');
        }
    }
}
