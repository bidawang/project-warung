<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransaksiKas;
use App\Models\PengeluaranPokokWarung;
use Illuminate\Support\Facades\Validator;

class TransaksiKasControllerAdmin extends Controller
{
    public function store(Request $request)
    {
        // dd($request->all());

        // =====================================================
        // VALIDASI
        // =====================================================
        $validator = Validator::make($request->all(), [
            'jenis' => 'required|in:masuk,keluar',
            'total' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255',
            'id_kas_warung' => 'required|exists:kas_warung,id',

            // optional
            'id_pengeluaran' => 'nullable|exists:pengeluaran_pokok_warung,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {

            // =====================================================
            // SIMPAN TRANSAKSI KAS
            // =====================================================
            TransaksiKas::create([
                'id_kas_warung' => $request->id_kas_warung,
                'total' => $request->total,
                'metode_pembayaran' => 'cash',
                'jenis' => $request->jenis,
                'keterangan' => $request->keterangan,
            ]);

            // =====================================================
            // UPDATE STATUS PENGELUARAN
            // =====================================================
            if ($request->filled('id_pengeluaran')) {

                PengeluaranPokokWarung::where('id', $request->id_pengeluaran)
                    ->update([
                        'status' => 'terpenuhi'
                    ]);
            }

            return redirect()
                ->back()
                ->with('success', 'Transaksi kas manual berhasil ditambahkan!');
        } catch (\Exception $e) {

            \Log::error(
                'Gagal menyimpan Transaksi Kas manual: ' . $e->getMessage()
            );

            return back()
                ->with('error', 'Gagal menyimpan transaksi kas. Silakan coba lagi.')
                ->withInput();
        }
    }
}
