<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kasir;
use App\Models\Member;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['kasir', 'member'])->latest()->get();
        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|in:kasir,member',
            'name' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'keterangan' => 'nullable|string',
        ]);

        // buat user
        $user = User::create($request->only(['role','name','nomor_hp','email','keterangan']));

        // buat kasir atau member
        if ($request->role === 'kasir') {
            Kasir::create([
                'id_user' => $user->id,
                'google_id' => $request->google_id,
                'keterangan' => $request->keterangan,
            ]);
        } elseif ($request->role === 'member') {
            Member::create([
                'id_user' => $user->id,
                'kode_user' => $request->kode_user,
                'keterangan' => $request->keterangan,
            ]);
        }

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:kasir,member',
            'name' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'keterangan' => 'nullable|string',
        ]);

        $user->update($request->only(['role','name','nomor_hp','email','keterangan']));

        // update relasi
        if ($user->role === 'kasir') {
            $user->kasir()->updateOrCreate(
                ['id_user' => $user->id],
                [
                    'google_id' => $request->google_id,
                    'keterangan' => $request->keterangan,
                ]
            );
            $user->member()->delete();
        } elseif ($user->role === 'member') {
            $user->member()->updateOrCreate(
                ['id_user' => $user->id],
                [
                    'kode_user' => $request->kode_user,
                    'keterangan' => $request->keterangan,
                ]
            );
            $user->kasir()->delete();
        }

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->kasir()->delete();
        $user->member()->delete();
        $user->delete();

        return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
    }
}
