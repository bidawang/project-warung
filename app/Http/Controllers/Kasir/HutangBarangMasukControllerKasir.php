<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HutangBarangMasuk;
use Illuminate\Support\Facades\Auth;

class HutangBarangMasukControllerKasir extends Controller
{
    /**
     * Menampilkan daftar hutang barang masuk.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Mendapatkan role pengguna yang sedang login
        $user = Auth::user();
        $role = $user->role;

        // Memulai query dengan eager loading relasi yang dibutuhkan
        $hutangQuery = HutangBarangMasuk::with(['barangMasuk']);

        // Jika user adalah kasir, filter berdasarkan id_warung dari session
        if ($role === 'kasir') {
            $id_warung = session('id_warung');
            $hutangQuery->where('id_warung', $id_warung);
        }

        // Filter berdasarkan status hutang
        $status = $request->get('status');
        if ($status && in_array($status, ['lunas', 'belum lunas'])) {
            $hutangQuery->whereHas('barangMasuk', function ($query) use ($status) {
                $query->where('status', $status);
            });
        }

        // Ambil data dengan paginasi
        $hutangList = $hutangQuery->paginate(10);

        // Mengembalikan view dengan data hutang
        return view('kasir.hutang_barang_masuk.index', compact('hutangList'));
    }
}
