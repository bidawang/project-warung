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
        $status = $request->status;
        $search = $request->q;

        // Base query untuk Aturan Tenggat (tetap sama)
        $aturanTenggats = AturanTenggat::with('warung')->get();

        // Query Utama: Kelompokkan berdasarkan User
        $query = Hutang::with(['user', 'warung'])
            ->select('id_user', 'id_warung') // Ambil kolom kunci
            ->selectRaw('SUM(jumlah_hutang_awal) as total_awal')
            ->selectRaw('SUM(jumlah_sisa_hutang) as total_sisa')
            ->selectRaw('COUNT(*) as total_nota')
            ->selectRaw('MIN(tenggat) as tenggat_terdekat')
            ->groupBy('id_user', 'id_warung');

        // Filter Search (Nama User)
        if ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        // Filter Status
        if ($status === 'lunas') {
            $query->havingRaw('SUM(jumlah_sisa_hutang) <= 0');
        } elseif ($status === 'belum_lunas') {
            $query->havingRaw('SUM(jumlah_sisa_hutang) > 0');
        }

        $hutangList = $query->paginate(10);

        return view('admin.hutang.index', compact('hutangList', 'aturanTenggats'));
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

    public function userDetail($userId)
    {
        $user = \App\Models\User::findOrFail($userId);

        // Sekarang pembayarans sudah terdaftar di model
        $hutangList = Hutang::with(['warung', 'pembayarans'])
            ->where('id_user', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSisa = $hutangList->sum('jumlah_sisa_hutang');
        $totalHutang = $hutangList->sum('jumlah_hutang_awal');

        return view('admin.hutang.user_detail', compact('user', 'hutangList', 'totalSisa', 'totalHutang'));
    }
}
