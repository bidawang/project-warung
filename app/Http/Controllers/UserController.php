<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kasir;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Tambahkan ini

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
            'password' => 'required|string|min:8|confirmed', // Validasi password baru
            'keterangan' => 'nullable|string',
        ]);

        // buat user
        $user = User::create([
            'role' => $request->role,
            'name' => $request->name,
            'nomor_hp' => $request->nomor_hp,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash password sebelum disimpan
            'keterangan' => $request->keterangan,
        ]);

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
            'password' => 'nullable|string|min:8|confirmed', // password tidak wajib, tapi harus valid jika diisi
            'keterangan' => 'nullable|string',
        ]);
        
        $userData = $request->only(['role','name','nomor_hp','email','keterangan']);

        // Jika password diisi, hash password baru
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

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