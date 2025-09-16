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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('barang')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // Ambil ID dari kategori & subkategori yang sudah ada
        $subKategoriMinumanDinginId = DB::table('sub_kategori')->where('sub_kategori', 'Minuman Dingin')->first()->id;
        $subKategoriKeripikId = DB::table('sub_kategori')->where('sub_kategori', 'Keripik')->first()->id;

        DB::table('barang')->insert([
            // Minuman
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
                'id_sub_kategori' => $subKategoriMinumanDinginId,
                'kode_barang' => 'M003',
                'nama_barang' => 'Sprite 390ml',
                'keterangan' => 'Minuman rasa lemon segar.',
            ],
            [
                'id_sub_kategori' => $subKategoriMinumanDinginId,
                'kode_barang' => 'M004',
                'nama_barang' => 'Fanta Orange 390ml',
                'keterangan' => 'Minuman rasa jeruk berkarbonasi.',
            ],
            [
                'id_sub_kategori' => $subKategoriMinumanDinginId,
                'kode_barang' => 'M005',
                'nama_barang' => 'Teh Botol Sosro 450ml',
                'keterangan' => 'Teh melati manis dalam botol.',
            ],
            [
                'id_sub_kategori' => $subKategoriMinumanDinginId,
                'kode_barang' => 'M006',
                'nama_barang' => 'Pocari Sweat 500ml',
                'keterangan' => 'Minuman isotonik untuk rehidrasi.',
            ],
            [
                'id_sub_kategori' => $subKategoriMinumanDinginId,
                'kode_barang' => 'M007',
                'nama_barang' => 'Good Day Cappuccino 250ml',
                'keterangan' => 'Kopi instan dingin rasa cappuccino.',
            ],
            [
                'id_sub_kategori' => $subKategoriMinumanDinginId,
                'kode_barang' => 'M008',
                'nama_barang' => 'Ultra Milk Coklat 200ml',
                'keterangan' => 'Susu UHT rasa coklat.',
            ],
            [
                'id_sub_kategori' => $subKategoriMinumanDinginId,
                'kode_barang' => 'M009',
                'nama_barang' => 'Milo Kotak 240ml',
                'keterangan' => 'Minuman energi rasa coklat.',
            ],
            [
                'id_sub_kategori' => $subKategoriMinumanDinginId,
                'kode_barang' => 'M010',
                'nama_barang' => 'Fruit Tea Blackcurrant 500ml',
                'keterangan' => 'Minuman teh buah rasa blackcurrant.',
            ],

            // Snack / Keripik
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
            [
                'id_sub_kategori' => $subKategoriKeripikId,
                'kode_barang' => 'S003',
                'nama_barang' => 'Qtela Singkong Balado',
                'keterangan' => 'Keripik singkong pedas manis balado.',
            ],
            [
                'id_sub_kategori' => $subKategoriKeripikId,
                'kode_barang' => 'S004',
                'nama_barang' => 'Taro Net Seaweed',
                'keterangan' => 'Snack jaring rasa rumput laut.',
            ],
            [
                'id_sub_kategori' => $subKategoriKeripikId,
                'kode_barang' => 'S005',
                'nama_barang' => 'Kusuka Singkong Original',
                'keterangan' => 'Keripik singkong rasa original.',
            ],
            [
                'id_sub_kategori' => $subKategoriKeripikId,
                'kode_barang' => 'S006',
                'nama_barang' => 'Piattos BBQ',
                'keterangan' => 'Keripik kentang rasa barbeque.',
            ],
            [
                'id_sub_kategori' => $subKategoriKeripikId,
                'kode_barang' => 'S007',
                'nama_barang' => 'Oishi Potato Chips',
                'keterangan' => 'Keripik kentang tipis renyah.',
            ],
            [
                'id_sub_kategori' => $subKategoriKeripikId,
                'kode_barang' => 'S008',
                'nama_barang' => 'Doritos Nacho Cheese',
                'keterangan' => 'Tortilla chips rasa keju nacho.',
            ],
            [
                'id_sub_kategori' => $subKategoriKeripikId,
                'kode_barang' => 'S009',
                'nama_barang' => 'Pringles Sour Cream & Onion',
                'keterangan' => 'Keripik kentang rasa sour cream.',
            ],
        ]);
    }
}
