<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Hutang;

class MemberControllerKasir extends Controller
{
    public function index()
    {
        //ambil data user
        $member = User::where('role', 'member')->get();

        return view('kasir.member.index', compact('member'));
    }

    public function create()
    {
        return view('kasir.member.create');
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $member = User::find($id);

        return view('kasir.member.edit', compact('member'));
    }

    public function update(Request $request, $id)
    {
        // validasi data
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255',
            'nomor_hp'   => 'required|string|max:20',
            'keterangan' => 'nullable|string',
        ]);

        // cari member berdasarkan id
        $member = User::findOrFail($id);

        // update data
        $member->update([
            'name'       => $request->name,
            'email'      => $request->email,
            'nomor_hp'   => $request->nomor_hp,
            'keterangan' => $request->keterangan,
        ]);

        // redirect dengan pesan sukses
        return redirect()->route('kasir.member.index')->with('success', 'Data member berhasil diperbarui.');
    }

    public function detail($id)
    {
        $member = User::find($id);
        $hutang = Hutang::where('id_user', $member->id)->get();

        return view('kasir.member.detail', compact('member', 'hutang'));
    }

    public function destroy($id)
    {
        $member = User::find($id);
        $member->delete();

        return redirect()->route('kasir.member.index')->with('success', 'Data member berhasil dihapus.');
    }
}
