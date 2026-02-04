<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarangMasuk;

class BelanjaBarangControllerAdmin extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = BarangMasuk::with([
            'stokWarung.barang',
            'stokWarung.warung',
            'transaksiBarang'
        ])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('stokWarung.barang', function ($inner) use ($search) {
                    $inner->where('nama_barang', 'like', "%{$search}%");
                })->orWhereHas('stokWarung.warung', function ($inner) use ($search) {
                    $inner->where('nama_warung', 'like', "%{$search}%");
                });
            });
        }

        $riwayatBelanja = $query->paginate(30); // Ambil lebih banyak karena akan di-group

        // Grouping di level Collection berdasarkan Warung dan Tanggal (Y-m-d)
        $groupedRiwayat = $riwayatBelanja->groupBy(function ($item) {
            return $item->stokWarung->warung->nama_warung . ' - ' . $item->created_at->format('Y-m-d');
        });

        return view('admin.belanjabarang.index', compact('riwayatBelanja', 'groupedRiwayat', 'search'));
    }
}
