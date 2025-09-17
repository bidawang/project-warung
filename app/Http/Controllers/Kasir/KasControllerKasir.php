<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransaksiKas; // Tambahkan model TransaksiKas
use App\Models\KasWarung; // Tambahkan model KasWarung

class KasControllerKasir extends Controller
{
    public function index()
{
    $idWarung = session('id_warung');

    if (!$idWarung) {
        return redirect()->route('dashboard')->with('error', 'ID warung tidak ditemukan di sesi.');
    }

    // ✅ Ambil kas warung dulu
$kasWarung = \App\Models\KasWarung::where('id_warung', $idWarung)
        ->where('jenis_kas', 'cash')
        ->first();
    if (!$kasWarung) {
        return redirect()->route('dashboard')->with('error', 'Kas warung tidak ditemukan.');
    }

    $idKasWarung = $kasWarung->id;

    // ✅ Hitung Total Pendapatan
    $totalPendapatan = \App\Models\TransaksiKas::where('id_kas_warung', $idKasWarung)
        ->where('jenis', 'penjualan')
        ->sum('total');
// dd($totalPendapatan);
    // ✅ Hitung Total Pengeluaran
    $totalPengeluaran = \App\Models\TransaksiKas::where('id_kas_warung', $idKasWarung)
        ->where('jenis', 'pengeluaran')
        ->sum('total');

    // ✅ Hitung Saldo Bersih
    $saldoBersih = $totalPendapatan - $totalPengeluaran;

    // ✅ Ambil riwayat transaksi
    $riwayatTransaksi = \App\Models\TransaksiKas::where('id_kas_warung', $idKasWarung)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('kasir.kas.index', compact(
        'totalPendapatan',
        'totalPengeluaran',
        'saldoBersih',
        'riwayatTransaksi'
    ));
}

}
