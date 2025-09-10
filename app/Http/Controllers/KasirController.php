<?php

namespace App\Http\Controllers;

use App\Models\Kasir;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index()
    {
        $kasirs = Kasir::with('user')->get();
        return view('kasir.index', compact('kasirs'));
    }

    public function show($id)
    {
        $kasir = Kasir::with('user')->findOrFail($id);
        return view('kasir.show', compact('kasir'));
    }
}
