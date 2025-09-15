<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warung;
use App\Models\KasWarung;
use App\Models\DetailKasWarung;
use App\Models\User;
use App\Models\Area;

class WarungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user & area dummy untuk relasi
        $user = User::first() ?? User::factory()->create();
        $area = Area::first() ?? Area::factory()->create();

        $warungs = [
            [
                'nama_warung' => 'Warung Sederhana',
                'modal' => 5000000,
                'keterangan' => 'Warung kecil pinggir jalan',
                'id_user' => 1,
            ],
            [
                'nama_warung' => 'Warung Maju Jaya',
                'modal' => 7500000,
                'keterangan' => 'Warung berkembang dengan pelanggan tetap',
                'id_user' => 2,
            ],
            [
                'nama_warung' => 'Warung Sejahtera',
                'modal' => 10000000,
                'keterangan' => 'Warung besar dengan banyak cabang',
                'id_user' => 3,
            ],
            [
                'nama_warung' => 'Warung Makmur',
                'modal' => 8500000,
                'keterangan' => 'Warung dengan modal menengah',
                'id_user' => 4,
            ],
            [
                'nama_warung' => 'Warung Berkah',
                'modal' => 6000000,
                'keterangan' => 'Warung yang selalu ramai setiap hari',
                'id_user' => 5,
            ],
        ];

        foreach ($warungs as $data) {
            $warung = Warung::create([
                'id_user' => $data['id_user'],
                'id_area' => $area->id,
                'nama_warung' => $data['nama_warung'],
                'modal' => $data['modal'],
                'keterangan' => $data['keterangan'],
            ]);

            // Kas jenis cash (saldo pasti 0)
            KasWarung::create([
                'id_warung' => $warung->id,
                'jenis_kas' => 'cash',
                'saldo' => 0,
                'keterangan' => 'Kas tunai selalu nol',
            ]);

            // Kas jenis bank (saldo ada isinya)
            $kasBank = KasWarung::create([
                'id_warung' => $warung->id,
                'jenis_kas' => 'bank',
                'saldo' => 2000000, // contoh saldo awal
                'keterangan' => 'Kas di bank',
            ]);

            // Detail kas bank
            DetailKasWarung::create([
                'id_kas_warung' => $kasBank->id,
                'pecahan' => '100000',
                'jumlah' => 10, // Rp 1.000.000
                'keterangan' => 'Pecahan seratus ribu',
            ]);

            DetailKasWarung::create([
                'id_kas_warung' => $kasBank->id,
                'pecahan' => '50000',
                'jumlah' => 20, // Rp 1.000.000
                'keterangan' => 'Pecahan lima puluh ribu',
            ]);
        }
    }
}
