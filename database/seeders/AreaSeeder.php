<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('area')->insert([
            [
                'area' => 'Banjarmasin Tengah',
                'keterangan' => 'Area mencakup pusat kota Banjarmasin.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area' => 'Banjarmasin Timur',
                'keterangan' => 'Area mencakup bagian timur kota Banjarmasin.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area' => 'Banjarbaru Utara',
                'keterangan' => 'Area mencakup wilayah Banjarbaru bagian utara.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area' => 'Martapura Kota',
                'keterangan' => 'Area khusus untuk wilayah perkotaan Martapura.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
