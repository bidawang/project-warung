<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use App\Models\Warung;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        // Mengambil semua data dari tabel warung
        $warungs = DB::table('warung')->select('id', 'nama_warung', 'keterangan', 'modal')->get();

        return view('admin.laporanlaba.select_warung', compact('warungs'));
    }

    public function showLaba($id_warung)
    {
        // ===============================
        // 1. AMBIL WARUNG + RELASI
        // ===============================
        $warung = Warung::with([
            'stokWarung.barangKeluar' => function ($q) {
                $q->where('jenis', 'penjualan barang');
            },
            'stokWarung.hargaJual'
        ])->findOrFail($id_warung);

        // ===============================
        // 2. HITUNG LABA
        // ===============================
        $labaKotor  = 0;
        $labaBersih = 0;

        foreach ($warung->stokWarung as $stok) {
            $hargaModal = $stok->hargaJual->harga_modal ?? 0;

            foreach ($stok->barangKeluar as $keluar) {
                $subtotalJual = $keluar->jumlah * $keluar->harga_jual;
                $subtotalModal = $keluar->jumlah * $hargaModal;

                $labaKotor  += $subtotalJual;
                $labaBersih += ($subtotalJual - $subtotalModal);
            }
        }

        // ===============================
        // 3. RETURN KE VIEW
        // ===============================
        return view('admin.laporanlaba.detail_laba', [
            'warung'       => $warung,
            'laba_kotor'   => $labaKotor,
            'laba_bersih'  => $labaBersih,
            'total_modal'  => $labaKotor - $labaBersih,
        ]);
    }
}
