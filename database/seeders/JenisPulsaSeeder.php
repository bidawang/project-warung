<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisPulsa;

class JenisPulsaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_jenis' => 'Pulsa Reguler'],
            ['nama_jenis' => 'Paket Data'],
            ['nama_jenis' => 'Token Listrik'],
        ];

        foreach ($data as $item) {
            JenisPulsa::firstOrCreate(
                ['nama_jenis' => $item['nama_jenis']],
                $item
            );
        }
    }
}
