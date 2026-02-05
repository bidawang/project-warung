<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiKas;
use App\Models\KasWarung;
use App\Models\DetailKasWarung;

class KasControllerKasir extends Controller
{
    /**
     * Menampilkan ringkasan kas warung dan riwayat transaksi.
     */
    public function index()
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'ID warung tidak ditemukan.');
        }

        // 1. Ambil Data Kas (Cash & Bank)
        $kasCash = KasWarung::where('id_warung', $idWarung)->where('jenis_kas', 'cash')->first();
        $kasBank = KasWarung::where('id_warung', $idWarung)->where('jenis_kas', 'bank')->first();

        // Pastikan minimal Kas Cash ada
        if (!$kasCash) {
            return redirect()->route('dashboard')->with('error', 'Kas warung cash belum dikonfigurasi.');
        }

        // 2. Hitung Statistik Cash
        $pendapatanCash = TransaksiKas::where('id_kas_warung', $kasCash->id)
            ->whereIn('jenis', ['penjualan barang', 'penjualan pulsa', 'masuk', 'inject'])->sum('total');
        $pengeluaranCash = TransaksiKas::where('id_kas_warung', $kasCash->id)
            ->whereIn('jenis', ['expayet', 'hilang', 'keluar', 'hutang barang', 'hutang pulsa'])->sum('total');
        $saldoCash = $pendapatanCash - $pengeluaranCash;

        // 3. Hitung Statistik Bank
        $pendapatanBank = 0;
        $pengeluaranBank = 0;
        $saldoHitungBank = 0;
        $saldoSistemBank = 0;

        if ($kasBank) {
            // Total dari riwayat transaksi
            $pendapatanBank = TransaksiKas::where('id_kas_warung', $kasBank->id)
                ->whereIn('jenis', ['penjualan barang', 'penjualan pulsa', 'masuk', 'inject'])->sum('total');

            $pengeluaranBank = TransaksiKas::where('id_kas_warung', $kasBank->id)
                ->whereIn('jenis', ['expayet', 'hilang', 'keluar', 'hutang barang', 'hutang pulsa'])->sum('total');

            // Saldo berdasarkan kalkulasi riwayat
            $saldoHitungBank = $pendapatanBank - $pengeluaranBank;

            // Saldo yang tercatat di tabel kas_warung (Saldo Sistem)
            $saldoSistemBank = $kasBank->saldo;
        }

        // 4. Riwayat Transaksi Gabungan (Hari Ini)
        $ids = collect([$kasCash->id, $kasBank->id ?? null])->filter()->toArray();
        $riwayatTransaksi = TransaksiKas::whereIn('id_kas_warung', $ids)
            ->whereNotIn('jenis', ['opname +', 'opname -'])
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();

        // 5. Pecahan Uang Fisik (Hanya untuk Cash)
        $pecahanKas = DetailKasWarung::where('id_kas_warung', $kasCash->id)
            ->orderBy('pecahan', 'asc')->get();
        $totalUangFisik = $pecahanKas->sum(fn($item) => $item->pecahan * $item->jumlah);

        return view('kasir.kas.index', compact(
            'kasCash',
            'kasBank',
            'pendapatanCash',
            'pengeluaranCash',
            'saldoCash',
            'pendapatanBank',
            'pengeluaranBank',
            'riwayatTransaksi',
            'pecahanKas',
            'totalUangFisik',
            'pengeluaranBank',
            'saldoHitungBank',
            'saldoSistemBank',
        ));
    }

    /**
     * Menampilkan formulir untuk membuat transaksi kas manual baru (Masuk atau Keluar).
     */
    public function create()
    {
        $idWarung = session('id_warung');
        // dd($idWarung);
        if (!$idWarung) {
            return redirect()->route('kasir.kas.index')->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        // Pastikan KasWarung 'cash' ada untuk mendapatkan id_kas_warung
        $kasWarung = KasWarung::where('id_warung', $idWarung)
            ->where('jenis_kas', 'cash')
            ->first();

        if (!$kasWarung) {
            return redirect()->route('kasir.kas.index')->with('error', 'Kas warung cash tidak ditemukan. Transaksi manual tidak dapat ditambahkan.');
        }

        $idKasWarung = $kasWarung->id;

        return view('kasir.kas.create', compact('idKasWarung'));
    }

    /**
     * Menyimpan transaksi kas manual baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'jenis' => 'required|in:masuk,keluar', // 'masuk' = Pemasukan, 'pengeluaran' = Pengeluaran
            'total' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255',
            'id_kas_warung' => 'required|exists:kas_warung,id', // Harus ada dan valid
        ]);
        // dd($validator);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // 2. Buat Transaksi Kas
            TransaksiKas::create([
                'id_kas_warung' => $request->id_kas_warung,
                'total' => $request->total,
                // Metode pembayaran diset 'cash' karena ini adalah kas manual di kas warung 'cash'
                'metode_pembayaran' => 'cash',
                // Jenis transaksi: 'masuk' (untuk kas masuk manual) atau 'pengeluaran' (untuk kas keluar manual)
                'jenis' => $request->jenis,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('kasir.kas.index')->with('success', 'Transaksi kas manual berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Gagal menyimpan Transaksi Kas manual: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan transaksi kas. Silakan coba lagi.')->withInput();
        }
    }
}
