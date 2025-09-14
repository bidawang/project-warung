<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategori')->insert([
            [
                'kategori' => 'Minuman',
                'keterangan' => 'Berisi berbagai jenis minuman, baik kemasan maupun seduh.',
            ],
            [
                'kategori' => 'Makanan Ringan',
                'keterangan' => 'Berisi aneka snack, keripik, dan biskuit.',
            ],
            [
                'kategori' => 'Kebutuhan Dapur',
                'keterangan' => 'Berisi bahan-bahan pokok dan bumbu masakan.',
            ],
            [
                'kategori' => 'Kebutuhan Sehari-hari',
                'keterangan' => 'Berisi sabun, pasta gigi, shampo, dan lain-lain.',
            ],
        ]);
    }
}