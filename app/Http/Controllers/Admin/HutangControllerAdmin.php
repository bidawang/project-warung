<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hutang;
use App\Models\Warung;
use App\Models\AturanTenggat;
use App\Models\LogPembayaranHutang;

class HutangControllerAdmin extends Controller
{
    public function index(Request $request)
    {
        $query = Hutang::with(['user', 'warung']);

        $status = $request->get('status');
        if ($status) {
            $query->where('status', $status);
        }

        if ($request->filled('expired')) {
            $query->where('status', 'belum_lunas')
                ->whereDate('tenggat', '<', now());
        }

        if ($request->filled('q')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%');
            });
        }

        // --- Perubahan Utama: Prioritaskan status 'belum_lunas' ---
        // 1. Urutkan berdasarkan status, 'belum_lunas' (0) akan muncul sebelum 'lunas' (1).
        // 2. Kemudian, urutkan berdasarkan tanggal tenggat terdekat (ASC).
        $hutangList = $query
            ->orderByRaw("status = 'lunas' ASC") // ASC = 0 (belum_lunas) lalu 1 (lunas)
            ->orderBy('tenggat', 'asc')
            ->paginate(10);

        // Data tambahan untuk View
        $aturanTenggats = AturanTenggat::with('warung')->get();
        $allWarungs = Warung::select('id', 'nama_warung')->get();

        return view('admin.hutang.index', compact('hutangList', 'status', 'aturanTenggats', 'allWarungs'));
    }


    /**
     * Menampilkan detail Hutang tertentu dari warung manapun.
     *
     * @param  int  $id ID Hutang
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function detailAllWarung($id)
    {
        // Mengambil data hutang dengan eager load semua relasi yang diperlukan.
        // Karena ini admin, kita eager load relasi 'warung' untuk mengetahui pemilik hutang.
        $hutang = Hutang::with([
            'user',
            'warung', // Tambahkan relasi warung untuk konteks admin
            'barangHutang.barangKeluar.stokWarung.barang',
            'barangHutang.barangKeluar.stokWarung.hargaJual',
        ])->findOrFail($id); // Gunakan findOrFail untuk 404 jika tidak ditemukan

        // Mengambil riwayat pembayaran untuk hutang ini, diurutkan dari yang terbaru
        $logPembayaran = LogPembayaranHutang::where('id_hutang', $hutang->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Mengembalikan view detail dengan data hutang dan log pembayaran
        // Asumsi view berada di 'admin.hutang.detail'
        return view('admin.hutang.detail', compact('hutang', 'logPembayaran'));
    }
}
