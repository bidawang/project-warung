<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID dari kategori dan subkategori yang sudah di-seed
        $subKategoriMinumanDinginId = DB::table('sub_kategori')->where('sub_kategori', 'Minuman Dingin')->first()->id;
        $subKategoriKeripikId = DB::table('sub_kategori')->where('sub_kategori', 'Keripik')->first()->id;
        
        DB::table('barang')->insert([
            [
                'id_sub_kategori' => $subKategoriMinumanDinginId,
                'kode_barang' => 'M001',
                'nama_barang' => 'Coca-Cola 330ml',
                'keterangan' => 'Minuman ringan berkarbonasi.',
            ],
            [
                'id_sub_kategori' => $subKategoriMinumanDinginId,
                'kode_barang' => 'M002',
                'nama_barang' => 'Aqua Botol 600ml',
                'keterangan' => 'Air mineral kemasan.',
            ],
            [
                'id_sub_kategori' => $subKategoriKeripikId,
                'kode_barang' => 'S001',
                'nama_barang' => 'Chitato Keripik Kentang',
                'keterangan' => 'Keripik kentang rasa sapi panggang.',
            ],
            [
                'id_sub_kategori' => $subKategoriKeripikId,
                'kode_barang' => 'S002',
                'nama_barang' => 'Lays Keripik Kentang',
                'keterangan' => 'Keripik kentang rasa rumput laut.',
            ],
        ]);
    }
}