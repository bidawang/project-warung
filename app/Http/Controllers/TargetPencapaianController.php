<?php

namespace App\Http\Controllers;

use App\Models\TargetPencapaian;
use App\Models\Warung;
use Illuminate\Http\Request;

class TargetPencapaianController extends Controller
{
    public function index()
    {
        $target = TargetPencapaian::with('warung')->get();
        return view('targetpencapaian.index', compact('target'));
    }

    public function create()
    {
        $warung = Warung::all();
        return view('targetpencapaian.create', compact('warung'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            'target_pencapaian' => 'required|numeric|min:0',
            'status_pencapaian' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        TargetPencapaian::create($request->all());

        return redirect()->route('targetpencapaian.index')->with('success', 'Target Pencapaian berhasil ditambahkan.');
    }

    public function show(TargetPencapaian $targetpencapaian)
    {
        $targetpencapaian->load('warung');
        return view('targetpencapaian.show', compact('targetpencapaian'));
    }

    public function edit(TargetPencapaian $targetpencapaian)
    {
        $warung = Warung::all();
        return view('targetpencapaian.edit', compact('targetpencapaian', 'warung'));
    }

    public function update(Request $request, TargetPencapaian $targetpencapaian)
    {
        $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            'target_pencapaian' => 'required|numeric|min:0',
            'status_pencapaian' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $targetpencapaian->update($request->all());

        return redirect()->route('targetpencapaian.index')->with('success', 'Target Pencapaian berhasil diperbarui.');
    }

    public function destroy(TargetPencapaian $targetpencapaian)
    {
        $targetpencapaian->delete();
        return redirect()->route('targetpencapaian.index')->with('success', 'Target Pencapaian berhasil dihapus.');
    }
}
