<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MutasiBarangControllerKasir extends Controller
{
    public function index()
    {
        return view('kasir.mutasi.index');
    }
}
