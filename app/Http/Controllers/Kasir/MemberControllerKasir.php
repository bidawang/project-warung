<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemberControllerKasir extends Controller
{
    public function index(){
        return view('kasir.member.index');
    }

    public function create(){
        return view('kasir.member.create');
    }

    public function edit(){
        return view('kasir.member.edit');
    }
}
