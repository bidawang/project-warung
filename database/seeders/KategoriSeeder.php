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
            // Kategori lama (dari contoh awal Anda)
            [
                'kategori' => 'Minuman',
                'keterangan' => 'Berisi berbagai jenis minuman, baik kemasan, botol, kaleng, cup, maupun seduh.',
            ],
            [
                'kategori' => 'Makanan Ringan', // Diperbarui menjadi 'MAKANAN' untuk konsistensi, tetapi saya akan pakai 'MAKANAN' saja dan mengabaikan 'Makanan Ringan' jika tidak ada di data baru. Karena di data Anda sekarang ada 'MAKANAN', saya buatkan kategori baru.
                'keterangan' => 'Kategori yang lebih luas untuk semua makanan instan selain Sembako.',
            ],
            [
                'kategori' => 'Kebutuhan Dapur',
                'keterangan' => 'Berisi bahan-bahan pokok dan bumbu masakan.',
            ],
            [
                'kategori' => 'Kebutuhan Sehari-hari',
                'keterangan' => 'Berisi sabun, pasta gigi, shampo, dan lain-lain.',
            ],

            // Kategori Baru dari data Anda
            [
                'kategori' => 'ATK',
                'keterangan' => 'Alat Tulis Kantor dan Kebutuhan Rumah Tangga (Bayi, Kosmetik, Obat, Pecah Belah, dll).',
            ],
            [
                'kategori' => 'MAKANAN',
                'keterangan' => 'Makanan ringan kemasan seperti Biskuit, Cokelat, Permen, dan Snack.',
            ],
            [
                'kategori' => 'PROJECT',
                'keterangan' => 'Item yang berhubungan dengan proyek tertentu, mungkin untuk kebutuhan barang dagangan non-reguler.',
            ],
            [
                'kategori' => 'PULSA',
                'keterangan' => 'Produk non-fisik seperti pulsa reguler, paket data, dan token PLN.',
            ],
            [
                'kategori' => 'ROKOK',
                'keterangan' => 'Berbagai jenis rokok, termasuk korek api.',
            ],
            [
                'kategori' => 'SEMBAKO',
                'keterangan' => 'Sembilan Bahan Pokok seperti Mie Instan, Tepung, Minyak, Sabun, dll.',
            ],
        ]);
    }
}
