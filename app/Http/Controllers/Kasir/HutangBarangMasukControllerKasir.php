<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HutangBarangMasuk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiKas;
use App\Models\KasWarung;
use Carbon\Carbon;

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
        // dd($hutangList);
        // Mengembalikan view dengan data hutang
        return view('kasir.hutang_barang_masuk.index', compact('hutangList'));
    }

    public function showDetailPembayaran($id)
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('kasir.kas.index')->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        // 1. Cari hutang
        $hutang = HutangBarangMasuk::with('barangMasuk')
            ->where('id', $id)
            ->where('id_warung', $idWarung)
            ->first();

        // 2. Logika Validasi Baru
        if (!$hutang) {
            return redirect()->route('kasir.hutang.index')->with('error', 'Hutang tidak ditemukan.');
        }

        // Cek status lunas berdasarkan kondisi id_barang_masuk
        if ($hutang->id_barang_masuk != null) {
            // Jika ini hutang barang fisik, cek status di tabel barang_masuk
            if ($hutang->barangMasuk->status_pembayaran === 'lunas') {
                return redirect()->route('kasir.hutang.index')->with('error', 'Hutang barang ini sudah lunas.');
            }
        } else {
            // Jika ini hutang non-fisik (Top-Up Pulsa), cek status di tabel hutang_barang_masuk itu sendiri
            if ($hutang->status_pembayaran === 'lunas') {
                return redirect()->route('kasir.hutang.index')->with('error', 'Hutang pulsa ini sudah lunas.');
            }
        }

        // 3. Ambil kas warung 'cash'
        $kasWarung = KasWarung::where('id_warung', $idWarung)
            ->where('jenis_kas', 'cash')
            ->first();

        if (!$kasWarung) {
            return redirect()->route('kasir.hutang.index')->with('error', 'Kas warung cash tidak ditemukan.');
        }

        $idKasWarung = $kasWarung->id;

        return view('kasir.hutang_barang_masuk.detail', compact('hutang', 'idKasWarung'));
    }

    /**
     * Memproses pembayaran hutang barang masuk.
     */
    public function processPembayaranHutang(Request $request, $id)
    {
        $idWarung = session('id_warung');

        // dd($request->all());
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'total_bayar' => 'required|numeric|min:1',
            'id_kas_warung' => 'required|exists:kas_warung,id', // id kas warung cash
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // 2. Ambil data hutang
            $hutang = HutangBarangMasuk::with('barangMasuk')
                ->where('id', $id)
                ->where('id_warung', $idWarung)
                ->first();

            // 1. Cek keberadaan data hutang
            if (!$hutang) {
                DB::rollBack();
                return redirect()->route('kasir.hutang.index')->with('error', 'Data hutang tidak ditemukan.');
            }

            // 2. Cek status lunas dengan pengkondisian null check
            $isLunas = false;

            if ($hutang->id_barang_masuk !== null) {
                // Jika hutang barang fisik, cek status melalui relasi barangMasuk
                $isLunas = ($hutang->barangMasuk && $hutang->barangMasuk->status_pembayaran === 'lunas');
            } else {
                // Jika hutang pulsa (id_barang_masuk null), cek status langsung di tabel hutang_barang_masuk
                $isLunas = ($hutang->status_pembayaran === 'lunas');
            }

            // 3. Eksekusi pengecekan akhir
            if ($isLunas) {
                DB::rollBack();
                return redirect()->route('kasir.hutang.index')->with('error', 'Transaksi gagal: Hutang ini sudah lunas sebelumnya.');
            }

            $totalHutang = $hutang->total;
            $totalBayar = $request->total_bayar;

            // Cek apakah jumlah bayar sesuai dengan jumlah hutang
            if ($totalBayar != $totalHutang) {
                DB::rollBack();
                return back()->with('error', 'Jumlah pembayaran harus sama persis dengan total hutang: Rp ' . number_format($totalHutang, 0, ',', '.'))->withInput();
            }

            // dd('TransaksiKas created');
            // 3. Catat Transaksi Kas (Pengeluaran)
            TransaksiKas::create([
                'id_kas_warung' => $request->id_kas_warung,
                'total' => $totalBayar,
                'metode_pembayaran' => 'cash', // Pembayaran hutang barang masuk pasti pengeluaran kas
                'jenis' => 'keluar',
                'keterangan' => 'Pembayaran Hutang Barang Masuk Tanggal: ' . \Carbon\Carbon::parse($hutang->created_at)->format('d-m-Y') . ' | Total: Rp ' . number_format($totalBayar, 0, ',', '.'),
                'tanggal' => Carbon::now(),
            ]);

            // 4. Update status Hutang Barang Masuk
            $hutang->status = 'lunas'; // Asumsi ada kolom 'status' di HutangBarangMasuk
            $hutang->save();

            // // 5. Update status Pembayaran di Barang Masuk
            // $barangMasuk = $hutang->barangMasuk;
            // $barangMasuk->status_pembayaran = 'lunas';
            // $barangMasuk->save();

            // Commit transaksi
            DB::commit();

            return redirect()->route('kasir.hutang.barangmasuk.index')->with('success', 'Pembayaran hutang barang masuk berhasil dan status diubah menjadi LUNAS!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal memproses pembayaran hutang: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses pembayaran hutang. Silakan coba lagi.')->withInput();
        }
    }

    // Fungsi create() dan store() untuk transaksi kas manual (dipertahankan)
    public function create()
    {
        $idWarung = session('id_warung');
        if (!$idWarung) {
            return redirect()->route('kasir.kas.index')->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        $kasWarung = KasWarung::where('id_warung', $idWarung)
            ->where('jenis_kas', 'cash')
            ->first();

        if (!$kasWarung) {
            return redirect()->route('kasir.kas.index')->with('error', 'Kas warung cash tidak ditemukan. Transaksi manual tidak dapat ditambahkan.');
        }

        $idKasWarung = $kasWarung->id;

        return view('kasir.kas.create', compact('idKasWarung'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'jenis' => 'required|in:masuk,keluar',
            'total' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255',
            'id_kas_warung' => 'required|exists:kas_warung,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // 2. Buat Transaksi Kas
            TransaksiKas::create([
                'id_kas_warung' => $request->id_kas_warung,
                'total' => $request->total,
                'metode_pembayaran' => 'cash',
                'jenis' => $request->jenis,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('kasir.kas.index')->with('success', 'Transaksi kas manual berhasil ditambahkan!');
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan Transaksi Kas manual: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan transaksi kas. Silakan coba lagi.')->withInput();
        }
    }
}
