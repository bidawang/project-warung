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
            return redirect()->route('dashboard')->with('error', 'ID warung tidak ditemukan di sesi.');
        }


        // Kas warung CASH
        $kasWarung = KasWarung::where('id_warung', $idWarung)
            ->where('jenis_kas', 'cash')
            ->first();


        if (!$kasWarung) {
            return redirect()->route('dashboard')->with('error', 'Kas warung cash tidak ditemukan.');
        }


        $idKasWarung = $kasWarung->id;


        // Total pendapatan
        $totalPendapatan = TransaksiKas::where('id_kas_warung', $idKasWarung)
            ->whereIn('jenis', ['penjualan barang', 'penjualan pulsa', 'masuk'])
            ->sum('total');


        // Total pengeluaran
        $totalPengeluaran = TransaksiKas::where('id_kas_warung', $idKasWarung)
            ->whereIn('jenis', ['expayet', 'hilang', 'keluar', 'hutang barang', 'hutang pulsa'])
            ->sum('total');


        $saldoBersih = $totalPendapatan - $totalPengeluaran;


        // Riwayat transaksi (exclude opname)
        $riwayatTransaksi = TransaksiKas::where('id_kas_warung', $idKasWarung)
            ->whereNotIn('jenis', ['opname +', 'opname -'])
            ->orderBy('created_at', 'desc')
            ->whereDate('created_at', today())
            ->get();


        // 👉 Pecahan kas (uang fisik)
        $pecahanKas = DetailKasWarung::where('id_kas_warung', $idKasWarung)
            ->orderBy('pecahan', 'asc')
            ->get();


        // Total uang fisik
        $totalUangFisik = $pecahanKas->sum(function ($item) {
            return $item->pecahan * $item->jumlah;
        });


        return view('kasir.kas.index', compact(
            'totalPendapatan',
            'totalPengeluaran',
            'saldoBersih',
            'riwayatTransaksi',
            'pecahanKas',
            'totalUangFisik'
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
