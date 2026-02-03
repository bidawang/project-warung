<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        // Mengambil semua data dari tabel warung
        $warungs = DB::table('warung')->select('id', 'nama_warung', 'keterangan', 'modal')->get();

        return view('admin.laporan.select_warung', compact('warungs'));
    }

    public function showLaba($id_warung)
    {
        $warung = DB::table('warung')->where('id', $id_warung)->first();

        $laporan = DB::table('barang_keluar')
            ->join('stok_warung', 'barang_keluar.id_stok_warung', '=', 'stok_warung.id')
            ->join('harga_jual', function ($join) {
                $join->on('stok_warung.id_barang', '=', 'harga_jual.id_barang')
                    ->on('stok_warung.id_warung', '=', 'harga_jual.id_warung');
            })
            ->select(
                // Laba Kotor = Total semua harga jual (Omset)
                DB::raw("SUM(barang_keluar.jumlah * barang_keluar.harga_jual) as laba_kotor"),
                // Laba Bersih = Total (Harga Jual - Harga Modal) x Jumlah
                DB::raw("SUM(barang_keluar.jumlah * (barang_keluar.harga_jual - harga_jual.harga_modal)) as laba_bersih")
            )
            ->where('stok_warung.id_warung', $id_warung)
            ->where('barang_keluar.jenis', 'penjualan')
            ->first();

        return view('admin.laporan.detail_laba', [
            'warung' => $warung,
            'laba_kotor' => $laporan->laba_kotor ?? 0,
            'laba_bersih' => $laporan->laba_bersih ?? 0,
            // Selisih untuk info HPP (Modal)
            'total_modal' => ($laporan->laba_kotor ?? 0) - ($laporan->laba_bersih ?? 0)
        ]);
    }
}
