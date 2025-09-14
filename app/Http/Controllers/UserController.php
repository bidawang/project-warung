<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'nomor_hp' => 'nullable|string|max:20|unique:users,nomor_hp',
            'email' => 'required|email|unique:users,email',
            'google_id' => 'required|string|unique:users,google_id',
            'password' => 'required|string|min:8|confirmed',
            'keterangan' => 'nullable|string',
        ]);

        User::create([
            'role' => $request->role,
            'name' => $request->name,
            'nomor_hp' => $request->nomor_hp,
            'email' => $request->email,
            'google_id' => $request->google_id,
            'password' => Hash::make($request->password),
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'nomor_hp' => 'nullable|string|max:20|unique:users,nomor_hp,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'google_id' => 'required|string|unique:users,google_id,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'keterangan' => 'nullable|string',
        ]);

        $userData = $request->only(['role','name','nomor_hp','email','google_id','keterangan']);

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
    }
}
