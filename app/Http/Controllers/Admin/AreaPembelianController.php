<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaPembelian;
use Illuminate\Http\Request;

class AreaPembelianController extends Controller
{
    public function index(Request $request)
    {
        $query = AreaPembelian::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('area', 'like', "%$search%");
        }

        $areas = $query->paginate(10);

        return view('admin.areapembelian.index', compact('areas'));
    }

    public function create()
    {
        return view('admin.areapembelian.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'area' => 'required|string|max:255|unique:area_pembelian,area',
            'markup' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        AreaPembelian::create($request->only(['area', 'markup', 'keterangan']));

        return redirect()->route('admin.areapembelian.index')->with('success', 'Area berhasil ditambahkan!');
    }

    public function edit(AreaPembelian $areapembelian)
    {
        return view('admin.areapembelian.edit', compact('areapembelian'));
    }

    public function update(Request $request, AreaPembelian $areapembelian)
    {
        $request->validate([
            'area' => 'required|string|max:255|unique:area_pembelian,area,' . $areapembelian->id,
            'markup' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $areapembelian->update($request->only(['area', 'markup', 'keterangan']));

        return redirect()->route('admin.areapembelian.index')->with('success', 'Area berhasil diperbarui!');
    }

    public function destroy(AreaPembelian $areapembelian)
    {
        $areapembelian->delete();

        return redirect()->route('admin.areapembelian.index')->with('success', 'Area berhasil dihapus!');
    }
}
