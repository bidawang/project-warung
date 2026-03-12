<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use App\Models\Warung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        // Mengambil semua data dari tabel warung
        $warungs = DB::table('warung')->select('id', 'nama_warung', 'keterangan', 'modal')->get();
        // dd($warungs);
        return view('admin.laporanlaba.select_warung', compact('warungs'));
    }


    public function showLaba(Request $request, $id)
{
    $warung = Warung::findOrFail($id);

    $historyLaba = \App\Models\BarangKeluar::whereHas('stokWarung', function($query) use ($id) {
            $query->where('id_warung', $id); // Sesuaikan dengan foreign key area/warungmu
        })
        ->with('stokWarung.barang')
        ->latest()
        ->paginate(10); // Ambil 10 data per load

    if ($request->ajax()) {
        // Balikkan hanya baris table (tr), bukan seluruh table
        return view('admin.laporanlaba._item_history', compact('historyLaba'))->render();
    }

    return view('admin.laporanlaba.detail_laba', compact('warung', 'historyLaba'));
}
}
