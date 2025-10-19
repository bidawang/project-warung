<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SaldoPulsaControllerAdmin extends Controller
{
    public function index()
    {
        return view('admin.saldo_pulsa.index');
    }
}
