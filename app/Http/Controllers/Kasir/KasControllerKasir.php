<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TransaksiKas;
use App\Models\KasWarung;

class KasControllerKasir extends Controller
{
    /**
     * Menampilkan ringkasan kas warung dan riwayat transaksi.
     */
    public function index()
    {
        $idWarung = session('id_warung');

        if (!$idWarung) {
            return redirect()->route('dashboard')->with('error', 'ID warung tidak ditemukan di sesi.');
        }

        // Ambil kas warung 'cash'
        $kasWarung = KasWarung::where('id_warung', $idWarung)
            ->where('jenis_kas', 'cash')
            ->first();

        if (!$kasWarung) {
            return redirect()->route('dashboard')->with('error', 'Kas warung cash tidak ditemukan.');
        }

        $idKasWarung = $kasWarung->id;

        // Hitung Total Pendapatan (Kas Masuk)
        $totalPendapatan = TransaksiKas::where('id_kas_warung', $idKasWarung)
            ->whereIn('jenis', ['penjualan barang', 'penjualan pulsa', 'masuk'])
            ->sum('total');

        // Hitung Total Pengeluaran (Kas Keluar)
        $totalPengeluaran = TransaksiKas::where('id_kas_warung', $idKasWarung)
            ->whereIn('jenis', ['expayet', 'hilang', 'keluar', 'hutang barang', 'hutang pulsa'])
            ->sum('total');

        // Hitung Saldo Bersih
        $saldoBersih = $totalPendapatan - $totalPengeluaran;

        // Ambil riwayat transaksi (DENGAN FILTER EXCLUDE OPNAME)
        $riwayatTransaksi = TransaksiKas::where('id_kas_warung', $idKasWarung)
            ->whereNotIn('jenis', ['opname +', 'opname -']) // Menambahkan syarat ini
            ->orderBy('created_at', 'desc')
            ->get();

        return view('kasir.kas.index', compact(
            'totalPendapatan',
            'totalPengeluaran',
            'saldoBersih',
            'riwayatTransaksi'
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
