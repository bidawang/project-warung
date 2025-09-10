<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::with('user')->get();
        return view('member.index', compact('members'));
    }

    public function show($id)
    {
        $member = Member::with('user')->findOrFail($id);
        return view('member.show', compact('member'));
    }
}
