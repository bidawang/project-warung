<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Warung;
use Illuminate\Http\Request;

class AssetControllerAdmin extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with('warung')->latest();

        if ($request->filled('warung')) {
            $query->where('id_warung', $request->warung);
        }

        $data = $query->paginate(15);
        $warungs = Warung::all();

        return view('admin.asset.index', compact('data', 'warungs'));
    }

    public function create()
    {
        $warungs = Warung::all();
        return view('admin.asset.create', compact('warungs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_warung'          => 'required|exists:warung,id',
            'nama'               => 'required|string|max:255',
            'harga_asset'        => 'required|numeric|min:0',
            'tanggal_pembelian'  => 'required|date',
            'total_dibayar'      => 'nullable|numeric|min:0',
            'sisa_pembayaran'    => 'nullable|numeric|min:0',
            'keterangan'         => 'nullable|string'
        ]);

        $validated['total_dibayar'] = $validated['total_dibayar'] ?? 0;
        $validated['sisa_pembayaran'] = $validated['sisa_pembayaran'] ?? $validated['harga_asset'];

        Asset::create($validated);

        return redirect()
            ->route('admin.asset.index')
            ->with('success', 'Asset berhasil ditambahkan.');
    }

    public function show(Asset $asset)
    {
        $asset->load(['warung', 'pelunasan']);
        return view('admin.asset.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $warungs = Warung::all();
        return view('admin.asset.edit', compact('asset', 'warungs'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'id_warung'          => 'required|exists:warung,id',
            'nama'               => 'required|string|max:255',
            'harga_asset'        => 'required|numeric|min:0',
            'tanggal_pembelian'  => 'required|date',
            'total_dibayar'      => 'nullable|numeric|min:0',
            'sisa_pembayaran'    => 'nullable|numeric|min:0',
            'keterangan'         => 'nullable|string'
        ]);

        $asset->update($validated);

        return redirect()
            ->route('admin.asset.index')
            ->with('success', 'Asset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return back()->with('success', 'Asset berhasil dihapus.');
    }
}
