<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\LaporanKasWarung;
use App\Models\DetailKasWarung; // Pastikan model ini ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanKasControllerKasir extends Controller
{
    public function index(Request $request)
    {
        // Filter pencarian
        $query = LaporanKasWarung::query();

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        $isFilledToday = LaporanKasWarung::whereDate('created_at', \Carbon\Carbon::today())
            ->where('tipe', 'adjustment')
            ->exists();

        // Grouping berdasarkan tanggal dan jam menit yang sama (Satu sesi input)
        $laporanKas = $query->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($data) {
                return $data->created_at->format('Y-m-d H:i');
            });

        return view('kasir.laporan-kas.index', compact('laporanKas', 'isFilledToday'));
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
}
