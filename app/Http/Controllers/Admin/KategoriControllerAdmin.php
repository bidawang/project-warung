<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KategoriControllerAdmin extends Controller
{
    /**
     * Menampilkan daftar semua kategori.
     */
    public function index()
    {
        $kategoris = Kategori::all();
        return view('admin.kategori.index', compact('kategoris'));
    }

    /**
     * Menampilkan formulir untuk membuat kategori baru.
     */
    public function create()
    {
        return view('admin.kategori.create');
    }

    /**
     * Menyimpan kategori baru ke database.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'kategori' => 'required|string|max:255|unique:kategori,kategori',
                'keterangan' => 'nullable|string',
            ]);

            Kategori::create($validatedData);

            return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan kategori. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menampilkan formulir untuk mengedit kategori tertentu.
     */
    public function edit(Kategori $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    /**
     * Memperbarui kategori yang ada di database.
     */
    public function update(Request $request, Kategori $kategori)
    {
        try {
            $validatedData = $request->validate([
                'kategori' => 'required|string|max:255|unique:kategori,kategori,' . $kategori->id,
                'keterangan' => 'nullable|string',
            ]);

            $kategori->update($validatedData);

            return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui kategori. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menghapus kategori dari database.
     */
    public function destroy(Kategori $kategori)
    {
        try {
            $kategori->delete();
            return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus kategori. Silakan coba lagi.');
        }
    }
}
