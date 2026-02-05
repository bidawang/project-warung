<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransaksiAwal;
use App\Models\TransaksiLainLain;
use Illuminate\Support\Facades\DB;

class OperasionalControllerAdmin extends Controller
{
    // app/Http/Controllers/Admin/OperasionalControllerAdmin.php

    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = TransaksiLainLain::with(['transaksiAwal'])->latest();

        if ($search) {
            $query->where('keterangan', 'like', "%{$search}%")
                ->orWhereHas('transaksiAwal', function ($q) use ($search) {
                    $q->where('keterangan', 'like', "%{$search}%");
                });
        }

        // Ambil data dulu, baru kelompokkan di level Collection
        $riwayatRaw = $query->get();
        $riwayatGrouped = $riwayatRaw->groupBy('id_transaksi_wrb');

        return view('admin.operasional.index', compact('riwayatGrouped', 'search'));
    }

    public function create()
    {
        return view('admin.operasional.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'keterangan_umum' => 'required|string',
            'lain_keterangan.*' => 'required|string',
            'lain_harga.*' => 'required|numeric|min:0',
        ]);

        // dd($request->all());

        DB::beginTransaction();
        try {
            $grandTotal = 0;

            // 1. Simpan Header Transaksi
            $transaksi = TransaksiAwal::create([
                'total' => 0, // Diupdate setelah loop
                'keterangan' => $request->keterangan_umum,
            ]);

            // 2. Simpan Detail Biaya (Lain-Lain)
            if ($request->lain_keterangan) {
                foreach ($request->lain_keterangan as $i => $ket) {
                    $harga = $request->lain_harga[$i] ?? 0;

                    if (!empty($ket)) {
                        TransaksiLainLain::create([
                            'id_transaksi_awal' => $transaksi->id,
                            'keterangan'       => $ket,
                            'harga'            => (float)$harga,
                        ]);
                        $grandTotal += $harga;
                    }
                }
            }

            // 3. Update Total & Potong Saldo Dana Utama
            $transaksi->update(['total' => $grandTotal]);

            if ($grandTotal > 0) {
                DB::table('dana_utama')
                    ->where('jenis_dana', 'wrb_old')
                    ->decrement('saldo', $grandTotal);
            }

            DB::commit();
            return redirect()->route('admin.operasional.index')->with('success', 'Biaya operasional berhasil dicatat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal simpan: ' . $e->getMessage()]);
        }
    }
}
