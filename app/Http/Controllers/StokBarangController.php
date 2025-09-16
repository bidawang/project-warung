<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokWarung;

class StokBarangController extends Controller
{
    public function index(){
        $stokBarang = StokWarung::with(['warung', 'barang'])->get();
        return view('stokbarang.index');
    }
}
