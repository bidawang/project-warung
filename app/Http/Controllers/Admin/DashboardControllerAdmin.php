<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View; // Pastikan untuk mengimpor kelas View
use App\Models\Warung;

class DashboardControllerAdmin extends Controller
{
    public function index(Request $request): View
    {
        $searchKeyword = $request->query('search');

        $query = Warung::query()->with('user');

        if ($searchKeyword) {
            $query->where('nama_warung', 'like', '%' . $searchKeyword . '%');
        }

        $warungs = $query->latest()->paginate(8);

        return view('admin.dashboard.index', compact('warungs', 'searchKeyword'));
    }
}
