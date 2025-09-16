<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KasirControllerAdmin extends Controller
{
    public function index()
    {
        return view('kasir.kasir.index');
    }
}
