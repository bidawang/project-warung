<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubKategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mendapatkan ID dari kategori yang sudah di-seed sebelumnya
        $minumanId = DB::table('kategori')->where('kategori', 'Minuman')->first()->id;
        $makananRinganId = DB::table('kategori')->where('kategori', 'Makanan Ringan')->first()->id;

        DB::table('sub_kategori')->insert([
            [
                'id_kategori' => $minumanId,
                'sub_kategori' => 'Minuman Dingin',
                'keterangan' => 'Minuman yang biasanya disimpan di kulkas.',
            ],
            [
                'id_kategori' => $minumanId,
                'sub_kategori' => 'Minuman Sachet',
                'keterangan' => 'Minuman bubuk atau sachet yang diseduh.',
            ],
            [
                'id_kategori' => $makananRinganId,
                'sub_kategori' => 'Keripik',
                'keterangan' => 'Berbagai macam keripik kemasan.',
            ],
            [
                'id_kategori' => $makananRinganId,
                'sub_kategori' => 'Biskuit',
                'keterangan' => 'Aneka biskuit dan wafer.',
            ],
        ]);
    }
}