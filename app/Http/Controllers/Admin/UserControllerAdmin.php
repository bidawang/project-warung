<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

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
            ->paginate(10); // Menampilkan 10 user per halaman

        return view('admin.user.index', compact('users', 'searchKeyword'));
    }
}
