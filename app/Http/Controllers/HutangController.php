<?php

namespace App\Http\Controllers;

use App\Models\Hutang;
use App\Models\User;
use App\Models\Warung;
use Illuminate\Http\Request;

class HutangController extends Controller
{
    public function index()
    {
        $hutang = Hutang::with(['user', 'warung'])->get();
        return view('hutang.index', compact('hutang'));
    }

    public function create()
    {
        $user = User::all();
        $warung = Warung::all();
        return view('hutang.create', compact('user', 'warung'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'id_user' => 'required|exists:users,id',
            'jumlah' => 'required|numeric|min:1',
            'tenggat' => 'required|date',
            'status' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        Hutang::create($request->all());

        return redirect()->route('hutang.index')->with('success', 'Data hutang berhasil ditambahkan.');
    }

    public function show(Hutang $hutang)
    {
        $hutang->load(['user', 'warung']);
        return view('hutang.show', compact('hutang'));
    }

    public function edit(Hutang $hutang)
    {
        $users = User::all();
        $warung = Warung::all();
        return view('hutang.edit', compact('hutang', 'users', 'warung'));
    }

    public function update(Request $request, Hutang $hutang)
    {
        $request->validate([
            'id_warung' => 'required|exists:warung,id',
            'id_user' => 'required|exists:users,id',
            'jumlah' => 'required|numeric|min:1',
            'tenggat' => 'required|date',
            'status' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $hutang->update($request->all());

        return redirect()->route('hutang.index')->with('success', 'Data hutang berhasil diperbarui.');
    }

    public function destroy(Hutang $hutang)
    {
        $hutang->delete();
        return redirect()->route('hutang.index')->with('success', 'Data hutang berhasil dihapus.');
    }
}
