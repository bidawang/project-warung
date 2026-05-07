<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HargaPulsa;
use App\Models\JenisPulsa;

class HargaPulsaControllerAdmin extends Controller
{
    public function index(Request $request)
    {
        $jenisPulsa = JenisPulsa::orderBy('nama_jenis')->get();

        $hargaPulsas = HargaPulsa::join(
            'jenis_pulsa',
            'harga_pulsa.jenis_pulsa_id',
            '=',
            'jenis_pulsa.id'
        )
            ->select(
                'harga_pulsa.*',
                'jenis_pulsa.nama_jenis'
            )
            ->when($request->search, function ($query) use ($request) {
                $query->where('harga_pulsa.jumlah_pulsa', 'like', '%' . $request->search . '%');
            })
            ->when($request->jenis_pulsa_id, function ($query) use ($request) {
                $query->where('harga_pulsa.jenis_pulsa_id', $request->jenis_pulsa_id);
            })
            ->orderBy('harga_pulsa.jumlah_pulsa', 'asc')
            ->get();

        return view('admin.harga_pulsa.index', compact('hargaPulsas', 'jenisPulsa'));
    }

    public function create()
    {
        $jenisPulsa = JenisPulsa::all();
        return view('admin.harga_pulsa.create', compact('jenisPulsa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jumlah_pulsa'    => 'required|integer|min:1',
            'harga_alomogada' => 'required|numeric|min:0',
            'harga_jual'      => 'required|numeric|min:0',
            'harga_hutang'    => 'required|numeric|min:0',
            'jenis_pulsa_id'  => 'required|exists:jenis_pulsa,id',
        ]);

        HargaPulsa::create([
            'jumlah_pulsa'    => $request->jumlah_pulsa,
            'harga_alomogada' => $request->harga_alomogada,
            'harga_jual'      => $request->harga_jual,
            'harga_hutang'    => $request->harga_hutang,
            'jenis_pulsa_id'  => $request->jenis_pulsa_id
        ]);

        return redirect()->route('admin.harga-pulsa.index')->with('success', 'Harga pulsa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $hargaPulsa = HargaPulsa::findOrFail($id);
        $jenisPulsa = JenisPulsa::all();
        return view('admin.harga_pulsa.edit', compact('hargaPulsa', 'jenisPulsa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_pulsa'    => 'required|integer|min:1',
            'harga_alomogada' => 'required|numeric|min:0',
            'harga_jual'      => 'required|numeric|min:0',
            'harga_hutang'    => 'required|numeric|min:0',
            'jenis_pulsa_id'  => 'required|exists:jenis_pulsa,id',
        ]);

        $hargaPulsa = HargaPulsa::findOrFail($id);
        $hargaPulsa->update([
            'jumlah_pulsa'    => $request->jumlah_pulsa,
            'harga_alomogada' => $request->harga_alomogada,
            'harga_jual'      => $request->harga_jual,
            'harga_hutang'    => $request->harga_hutang,
            'jenis_pulsa_id'  => $request->jenis_pulsa_id
        ]);

        return redirect()->route('admin.harga-pulsa.index')->with('success', 'Harga pulsa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $hargaPulsa = HargaPulsa::findOrFail($id);
        $hargaPulsa->delete();
        return redirect()->route('admin.harga-pulsa.index')->with('success', 'Harga pulsa berhasil dihapus.');
    }
}
