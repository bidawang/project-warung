<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\LaporanKasWarung;
use App\Models\DetailKasWarung; // Pastikan model ini ada
use App\Models\LaporanBankWarung;
use App\Models\KasWarung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanKasControllerKasir extends Controller
{
    public function index(Request $request)
    {
        /* =======================
     * BASE QUERY
     * ======================= */
        $filterKas  = LaporanKasWarung::query();
        $filterBank = LaporanBankWarung::query();

        /* =======================
     * FILTER TANGGAL / BULAN / TAHUN
     * ======================= */
        if ($request->filled('tanggal')) {
            $filterKas->whereDate('created_at', $request->tanggal);
            $filterBank->whereDate('created_at', $request->tanggal);
        }

        if ($request->filled('bulan')) {
            $filterKas->whereMonth('created_at', $request->bulan);
            $filterBank->whereMonth('created_at', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $filterKas->whereYear('created_at', $request->tahun);
            $filterBank->whereYear('created_at', $request->tahun);
        }

        /* =======================
     * CEK AUDIT HARI INI (PISAH)
     * ======================= */
        $isFilledKasToday = LaporanKasWarung::whereDate('created_at', Carbon::today())
            ->where('tipe', 'adjustment')
            ->exists();

        $isFilledBankToday = LaporanBankWarung::whereDate('created_at', Carbon::today())
            ->where('tipe', 'adjustment')
            ->exists();

        /* =======================
     * DATA KAS (GROUP PER SESI)
     * ======================= */
        $laporanKas = $filterKas
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn($row) => $row->created_at->format('Y-m-d H:i'));

        /* =======================
     * DATA BANK (GROUP PER SESI)
     * ======================= */
        $laporanBank = $filterBank
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(fn($row) => $row->created_at->format('Y-m-d H:i'));

        return view('kasir.laporan-kas.index', compact(
            'laporanKas',
            'laporanBank',
            'isFilledKasToday',
            'isFilledBankToday'
        ));
    }

    public function store(Request $request)
    {
        $exists = LaporanKasWarung::whereDate('created_at', Carbon::today())->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'Laporan hari ini sudah diproses!');
        }

        $request->validate([
            'data.*.pecahan' => 'required|numeric',
            'data.*.jumlah' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Kita asumsikan id_kas_warung yang aktif adalah 1 (atau sesuaikan dengan logic aplikasi Anda)
            $idKasWarung = 1;

            foreach ($request->data as $row) {
                $pecahan = $row['pecahan'];
                $jumlahFisik = $row['jumlah'] ?? 0;

                // 1. Ambil data stok uang sistem saat ini (DetailKasWarung)
                $stokSistem = DetailKasWarung::where('id_kas_warung', $idKasWarung)
                    ->where('pecahan', $pecahan)
                    ->first();

                $jumlahSistem = $stokSistem ? $stokSistem->jumlah : 0;

                // 2. Simpan "Snaphot" sistem sebelum diubah (Tipe: Laporan)
                LaporanKasWarung::create([
                    'id_kas_warung' => $idKasWarung,
                    'pecahan' => $pecahan,
                    'jumlah' => $jumlahSistem,
                    'tipe' => 'laporan'
                ]);

                // 3. Simpan hasil input fisik kasir (Tipe: Adjustment)
                LaporanKasWarung::create([
                    'id_kas_warung' => $idKasWarung,
                    'pecahan' => $pecahan,
                    'jumlah' => $jumlahFisik,
                    'tipe' => 'adjustment'
                ]);

                // 4. Update Stok Sistem (DetailKasWarung) agar sinkron dengan uang fisik
                DetailKasWarung::updateOrCreate(
                    ['id_kas_warung' => $idKasWarung, 'pecahan' => $pecahan],
                    ['jumlah' => $jumlahFisik]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Laporan berhasil disimpan dan stok kas telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function storeBank(Request $request)
    {
        $exists = LaporanBankWarung::whereDate('created_at', Carbon::today())->exists();
        if ($exists) {
            return back()->with('error', 'Laporan bank hari ini sudah diproses!');
        }

        $request->validate([
            'jumlah' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // ambil kas_warung jenis bank (contoh: 1 akun bank aktif)
            $bank = KasWarung::where('jenis_kas', 'bank')->firstOrFail();

            $saldoSistem = $bank->saldo ?? 0;
            $saldoFisik  = $request->jumlah;

            // snapshot sistem
            LaporanBankWarung::create([
                'id_kas_warung' => $bank->id,
                'jumlah' => $saldoSistem,
                'tipe' => 'laporan',
            ]);

            // adjustment
            LaporanBankWarung::create([
                'id_kas_warung' => $bank->id,
                'jumlah' => $saldoFisik,
                'tipe' => 'adjustment',
            ]);

            // update saldo bank
            $bank->update(['saldo' => $saldoFisik]);

            DB::commit();
            return back()->with('success', 'Laporan bank berhasil disinkronkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
