<?php

namespace App\Http\Controllers\Pendukung;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\SubKategori;
use App\Models\Barang;
use Maatwebsite\Excel\Facades\Excel;
class InsertBarangController extends Controller
{
    public function index()
    {
        return view('Pendukung.insertbarang');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $data = Excel::toArray([], $request->file('file'));
        $rows = $data[0]; // sheet pertama

        unset($rows[0]); // hapus header

        foreach ($rows as $row) {

            $kategoriNama    = trim($row[0]);
            $subKategoriNama = trim($row[1]);
            $kodeBarang      = trim($row[2]);
            $namaBarang      = trim($row[3]);

            if (!$kategoriNama || !$subKategoriNama || !$kodeBarang || !$namaBarang) {
                continue; // skip baris kosong
            }

            // 1. KATEGORI
            $kategori = Kategori::firstOrCreate(
                ['kategori' => $kategoriNama],
                ['keterangan' => null]
            );

            // 2. SUB KATEGORI
            $subKategori = SubKategori::firstOrCreate(
                [
                    'id_kategori' => $kategori->id,
                    'sub_kategori' => $subKategoriNama
                ],
                ['keterangan' => null]
            );

            // 3. BARANG
            Barang::firstOrCreate(
                [
                    'id_sub_kategori' => $subKategori->id,
                    'kode_barang'     => $kodeBarang
                ],
                [
                    'nama_barang' => $namaBarang,
                    'keterangan'  => null
                ]
            );
        }

        return redirect()->back()->with('success', 'Import Excel berhasil 🚀');
    }

}
