<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AturanTenggat;
use App\Models\Hutang;
use App\Models\LogPembayaranHutang;
use App\Models\Warung;
use Illuminate\Http\Request;

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
// dd($hutangList);
        return view('admin.hutang.index', compact('hutangList', 'aturanTenggats'));
    }

    /**
     * Menampilkan detail Hutang tertentu dari warung manapun.
     *
     * @param  int  $id  ID Hutang
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
// dd($hutang, $logPembayaran);
        // Mengembalikan view detail dengan data hutang dan log pembayaran
        // Asumsi view berada di 'admin.hutang.detail'
        return view('admin.hutang.detail', compact('hutang', 'logPembayaran'));
    }

    public function userDetail($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
// dd($user);
        // Ambil hutang dengan relasi warung dan aturan_tenggat
        $hutangRaw = Hutang::with(['warung.aturanTenggat', 'pembayarans'])
            ->where('id_user', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Mapping data untuk menghitung bunga di level Controller
        $hutangList = $hutangRaw->map(function ($h) {
            $isOverdue = \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($h->tenggat)) && $h->status != 'lunas';

            $aturan = $h->warung->aturanTenggat ?? null;
            $nominalRekomendasi = 0;

            if ($aturan && $isOverdue) {
                $nilaiBungaDB = (float) $aturan->bunga;

                    // Hitung: (5 / 100) * 40000 = 2000
                    $nominalRekomendasi = ($nilaiBungaDB / 100) * (float) $h->jumlah_sisa_hutang;
                    // dd($h->jumlah_sisa_hutang, $nilaiBungaDB, $nominalRekomendasi);
                
            }

            // Tambahkan atribut virtual ke object hutang agar bisa dipanggil di View
            $h->is_overdue = $isOverdue;
            $h->rekomendasi_bunga = $nominalRekomendasi;

            return $h;
        });

// dd($hutangList);
        $totalSisa = $hutangList->sum('jumlah_sisa_hutang');
        $totalHutang = $hutangList->sum('jumlah_hutang_awal');

        return view('admin.hutang.user_detail', compact('user', 'hutangList', 'totalSisa', 'totalHutang'));
    }

    public function updateBunga(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'hutang_id' => 'required|exists:hutang,id',
            'nominal_bunga' => 'required|numeric|min:0',
        ]);

        // 2. Cari data hutang
        $hutang = Hutang::findOrFail($request->hutang_id);

        // 3. Update total_bunga dan jumlah_sisa_hutang
        // Bunga ditambahkan ke sisa hutang yang ada
        $bungaBaru = $request->nominal_bunga;

        $hutang->total_bunga += $bungaBaru;
        $hutang->jumlah_sisa_hutang += $bungaBaru;
        $hutang->save();

        // 4. Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Bunga sebesar Rp ' . number_format($bungaBaru, 0, ',', '.') . ' berhasil ditambahkan.');
    }
}
