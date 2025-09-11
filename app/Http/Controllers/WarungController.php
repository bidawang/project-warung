<?php

namespace App\Http\Controllers;

use App\Models\Warung;
use App\Models\User;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class WarungController extends Controller
{
    public function index()
    {
        $warungs = Warung::with(['user', 'area'])->get();
        return view('warung.index', compact('warungs'));
    }

    public function create()
    {
        $users = User::all();
        $areas = Area::all();
        return view('warung.create', compact('users', 'areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_area' => 'required|exists:area,id',
            'nama_warung' => 'required|string|max:255',
            'modal' => 'required|numeric',
            'keterangan' => 'nullable|string',
        ]);

        Warung::create($request->all());

        return redirect()->route('warung.index')->with('success', 'Warung berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $warung = Warung::findOrFail($id);
        $users = User::all();
        $areas = Area::all();
        return view('warung.edit', compact('warung', 'users', 'areas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'id_area' => 'required|exists:area,id',
            'nama_warung' => 'required|string|max:255',
            'modal' => 'required|numeric',
            'keterangan' => 'nullable|string',
        ]);

        $warung = Warung::findOrFail($id);
        $warung->update($request->all());

        return redirect()->route('warung.index')->with('success', 'Warung berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $warung = Warung::findOrFail($id);
        $warung->delete();

        return redirect()->route('warung.index')->with('success', 'Warung berhasil dihapus.');
    }

    public function show($id)
    {
        // Menambahkan where clause untuk memastikan warung hanya bisa dilihat oleh user yang memiliki ID_USER yang sama
        $warung = Warung::with(['user', 'area', 'stokWarung.barang'])
            ->where('id_user', 1)
            ->findOrFail($id);
        // $warung = Warung::with(['user', 'area'])
        //     ->where('id_user', Auth::id())
        //     ->findOrFail($id);

        return view('warung.show', compact('warung'));
    }
}
