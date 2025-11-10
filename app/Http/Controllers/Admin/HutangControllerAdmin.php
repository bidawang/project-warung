<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hutang;
use App\Models\LogPembayaranHutang;

class HutangControllerAdmin extends Controller
{
    public function index(Request $request)
    {
        // Query dasar: Ambil semua Hutang, eager load relasi user dan warung
        $query = Hutang::with(['user', 'warung']);

        // Filter berdasarkan status hutang ('belum lunas', 'lunas', atau null)
        $status = $request->get('status');
        if ($status) {
            $query->where('status', $status);
        }

        // Pencarian berdasarkan nama user
        if ($request->filled('q')) {
            $query->whereHas('user', function ($q) use ($request) {
                // Mencari nama user yang mengandung string pencarian
                $q->where('name', 'like', '%' . $request->q . '%');
            });
        }

        // Urutkan berdasarkan tenggat terdekat dan lakukan paginasi
        $hutangList = $query->orderBy('tenggat', 'asc')->paginate(10);

        // Mengembalikan view admin dengan data
        // Asumsi view berada di 'admin.hutang.index'
        return view('admin.hutang.index', compact('hutangList', 'status'));
    }

    /**
     * Menampilkan detail Hutang tertentu dari warung manapun.
     *
     * @param  int  $id ID Hutang
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function detail($id)
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
