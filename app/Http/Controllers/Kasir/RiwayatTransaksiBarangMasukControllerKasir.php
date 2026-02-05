<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\BarangMasuk;
use App\Models\KasWarung;

class RiwayatTransaksiBarangMasukControllerKasir extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $search = $request->get('search');

        $query = BarangMasuk::with([
            'stokWarung.barang',
            'transaksiBarang',
        ])
            ->whereHas('stokWarung.warung', function ($q) use ($userId) {
                $q->where('id_user', $userId);
            })
            ->latest();

        // SEARCH
        if ($search) {
            $query->whereHas('stokWarung.barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%");
            });
        }

        $riwayatBarangMasuk = $query->paginate(10);
// dd($riwayatBarangMasuk);
        return view(
            'kasir.riwayat_barang_masuk.index',
            compact('riwayatBarangMasuk', 'search')
        );
    }
}
