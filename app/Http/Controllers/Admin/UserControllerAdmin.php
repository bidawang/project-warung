<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserControllerAdmin extends Controller
{
    public function index(Request $request)
    {
        $searchKeyword = $request->get('search');
        $users = User::query()
            ->when($searchKeyword, function ($query, $searchKeyword) {
                $query->where('name', 'like', "%{$searchKeyword}%")
                    ->orWhere('email', 'like', "%{$searchKeyword}%");
            })
            ->latest()
            ->paginate(10);

        return view('admin.user.index', compact('users', 'searchKeyword'));
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:kasir,user',
            'nomor_hp' => 'nullable|string|max:15',
            'password' => 'required|min:6', // Tambahkan password jika perlu login
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'nomor_hp' => $request->nomor_hp,
            'keterangan' => $request->keterangan,
            'password' => Hash::make($request->password),
        ]);

        return redirect('admin/user')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        // dd($user);
        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:kasir,user',
            'nomor_hp' => 'nullable|string|max:15',
        ]);

        $data = $request->only(['name', 'email', 'role', 'nomor_hp', 'keterangan']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect('admin/user')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect('admin/user')->with('success', 'User berhasil dihapus.');
    }
}
