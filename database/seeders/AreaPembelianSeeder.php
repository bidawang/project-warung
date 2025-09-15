<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaPembelianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('area_pembelian')->insert([
            [
                'area' => 'Jakarta',
                'markup' => 10.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area' => 'Bandung',
                'markup' => 8.75,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area' => 'Surabaya',
                'markup' => 12.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area' => 'Yogyakarta',
                'markup' => 7.25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
