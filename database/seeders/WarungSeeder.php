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
        // Pastikan ada user dan area dummy jika belum ada
        $area = Area::first() ?? Area::factory()->create();

        // Ambil 5 user Kasir yang baru dibuat di UserSeeder (dengan email kasir1@example.com s/d kasir5@example.com)
        $kasirUsers = User::where('role', 'kasir')
                          ->whereIn('email', ['kasir1@example.com', 'kasir2@example.com', 'kasir3@example.com', 'kasir4@example.com', 'kasir5@example.com'])
                          ->orderBy('id')
                          ->limit(5)
                          ->get();

        // Pastikan kita memiliki 5 kasir
        if ($kasirUsers->count() < 5) {
            echo "Peringatan: Tidak dapat menemukan 5 user kasir spesifik. Harap pastikan UserSeeder dijalankan terlebih dahulu.\n";
            return;
        }

        // Data warung
        $warungsData = [
            [
                'nama_warung' => 'Warung Sederhana',
                'modal' => 5000000,
                'keterangan' => 'Warung kecil pinggir jalan',
            ],
            [
                'nama_warung' => 'Warung Maju Jaya',
                'modal' => 7500000,
                'keterangan' => 'Warung berkembang dengan pelanggan tetap',
            ],
            [
                'nama_warung' => 'Warung Sejahtera',
                'modal' => 10000000,
                'keterangan' => 'Warung besar dengan banyak cabang',
            ],
            [
                'nama_warung' => 'Warung Makmur',
                'modal' => 8500000,
                'keterangan' => 'Warung dengan modal menengah',
            ],
            [
                'nama_warung' => 'Warung Berkah',
                'modal' => 6000000,
                'keterangan' => 'Warung yang selalu ramai setiap hari',
            ],
        ];

        // Looping untuk membuat Warung dan KasWarung
        foreach ($warungsData as $index => $data) {
            // Dapatkan ID Kasir secara berurutan (Kasir ke-0 untuk Warung ke-0, dst.)
            $kasirId = $kasirUsers[$index]->id;

            $warung = Warung::create([
                'id_user' => $kasirId, // Menetapkan Kasir ID
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
            ]);

            DetailKasWarung::create([
                'id_kas_warung' => $kasBank->id,
                'pecahan' => '50000',
                'jumlah' => 20, // Rp 1.000.000
            ]);
        }
    }
}
