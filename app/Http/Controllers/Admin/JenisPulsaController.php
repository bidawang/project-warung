<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JenisPulsa;

class JenisPulsaController extends Controller
{
    public function index()
    {
        $jenisPulsa = JenisPulsa::orderBy('nama_jenis')->get();
        return view('admin.jenis_pulsa.index', compact('jenisPulsa'));
    }

    public function create()
    {
        return view('admin.jenis_pulsa.create');
    }

    /**
     * STORE (support form & AJAX)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:50|unique:jenis_pulsa,nama_jenis'
        ], [
            'nama_jenis.required' => 'Nama jenis pulsa wajib diisi',
            'nama_jenis.unique'   => 'Jenis pulsa sudah ada'
        ]);

        $jenis = JenisPulsa::create([
            'nama_jenis' => $request->nama_jenis
        ]);

        // 👉 Jika request dari AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $jenis
            ]);
        }

        // 👉 Jika dari form biasa
        return redirect()
            ->route('admin.jenis-pulsa.index')
            ->with('success', 'Jenis pulsa berhasil ditambahkan');
    }

    public function edit($id)
    {
        $jenisPulsa = JenisPulsa::findOrFail($id);
        return view('admin.jenis_pulsa.edit', compact('jenisPulsa'));
    }

    public function update(Request $request, $id)
    {
        $jenisPulsa = JenisPulsa::findOrFail($id);

        $request->validate([
            'nama_jenis' => 'required|string|max:50|unique:jenis_pulsa,nama_jenis,' . $jenisPulsa->id
        ], [
            'nama_jenis.required' => 'Nama jenis pulsa wajib diisi',
            'nama_jenis.unique'   => 'Jenis pulsa sudah ada'
        ]);

        $jenisPulsa->update([
            'nama_jenis' => $request->nama_jenis
        ]);

        return redirect()
            ->route('admin.jenis-pulsa.index')
            ->with('success', 'Jenis pulsa berhasil diperbarui');
    }

    public function destroy($id)
    {
        $jenisPulsa = JenisPulsa::findOrFail($id);

        // Proteksi FK (kalau sudah dipakai di harga pulsa)
        if ($jenisPulsa->hargaPulsa()->count() > 0) {
            return back()->with('error', 'Jenis pulsa tidak bisa dihapus karena masih digunakan');
        }

        $jenisPulsa->delete();

        return redirect()
            ->route('admin.jenis-pulsa.index')
            ->with('success', 'Jenis pulsa berhasil dihapus');
    }
}
