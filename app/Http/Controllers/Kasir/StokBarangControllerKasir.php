<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StokBarangControllerKasir extends Controller
{
    public function index()
    {
        return view('kasir.stok_barang.index');
    }

    public function barangMasuk()
    {
        return view('kasir.stok_barang.barang_masuk');
    }
}
