<?php

namespace App\Http\Controllers\Pendukung;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laba;
use App\Models\Area;
use Maatwebsite\Excel\Facades\Excel;

class LabaController extends Controller
{
    public function formImport()
    {
        $areas = Area::orderBy('area')->get();
        return view('Pendukung.insertlaba', compact('areas'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'id_area' => 'required|exists:area,id',
            'file'    => 'required|mimes:xlsx,xls'
        ]);

        $rows = Excel::toArray([], $request->file('file'))[0];
        unset($rows[0]); // buang header

        $grouped = [];

        // 1. Group modal berdasarkan harga
        foreach ($rows as $row) {
            $modal = isset($row[0]) ? (int) $row[0] : null;
            $harga = isset($row[1]) ? (int) $row[1] : null;

            if (!$modal || !$harga) {
                continue;
            }

            $grouped[$harga][] = $modal;
        }

        // 2. Simpan ke tabel laba
        foreach ($grouped as $hargaJual => $modals) {

            Laba::updateOrCreate(
                [
                    'id_area'    => $request->id_area,
                    'harga_jual' => $hargaJual,
                ],
                [
                    'input_minimal'  => min($modals),
                    'input_maksimal' => max($modals),
                ]
            );
        }

        return back()->with('success', 'Import laba berhasil (modal → range otomatis)');
    }
}
