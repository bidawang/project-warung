<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengeluaranPokokWarung;
use App\Models\Warung;
use Illuminate\Http\Request;

class PengeluaranPokokWarungControllerAdmin extends Controller
{
    // =========================
    // OPTION 1 & 2 DI SINI
    // =========================
    public function index(Request $request)
    {
        $query = PengeluaranPokokWarung::with('warung')->latest();

        // OPTION 2: filter jika warung dipilih
        if ($request->filled('warung')) {
            $query->where('id_warung', $request->warung);
        }

        $data = $query->paginate(15);
        $warungs = Warung::all();

        return view('admin.pengeluaran_pokok_warung.index', compact('data', 'warungs'));
    }

    public function create()
    {
        $warungs = Warung::all();
        return view('admin.pengeluaran_pokok_warung.create', compact('warungs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'redaksi'   => 'required|string|max:255',
            'jumlah'    => 'required|numeric|min:0',
            'date'      => 'required|date',
        ]);

        $validated['status'] = 'belum terpenuhi';

        PengeluaranPokokWarung::create($validated);

        return redirect()
            ->route('admin.pengeluaran-pokok-warung.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function show(PengeluaranPokokWarung $pengeluaran_pokok_warung)
    {
        $pengeluaran_pokok_warung->load('warung');
        return view('admin.pengeluaran_pokok_warung.show', compact('pengeluaran_pokok_warung'));
    }

    public function edit(PengeluaranPokokWarung $pengeluaran_pokok_warung)
    {
        if ($pengeluaran_pokok_warung->status !== 'belum terpenuhi') {
            abort(403);
        }

        $warungs = Warung::all();

        return view(
            'admin.pengeluaran_pokok_warung.edit',
            compact('pengeluaran_pokok_warung', 'warungs')
        );
    }

    public function update(Request $request, PengeluaranPokokWarung $pengeluaran_pokok_warung)
    {
        if ($pengeluaran_pokok_warung->status !== 'belum terpenuhi') {
            abort(403);
        }

        $validated = $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'redaksi'   => 'required|string|max:255',
            'jumlah'    => 'required|numeric|min:0',
            'date'      => 'required|date',
        ]);

        $pengeluaran_pokok_warung->update($validated);

        return redirect()
            ->route('admin.pengeluaran-pokok-warung.index')
            ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(PengeluaranPokokWarung $pengeluaran_pokok_warung)
    {
        if ($pengeluaran_pokok_warung->status !== 'belum terpenuhi') {
            abort(403);
        }

        $pengeluaran_pokok_warung->delete();

        return back()->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
