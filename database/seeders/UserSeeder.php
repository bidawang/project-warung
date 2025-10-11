<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Membuat User Admin (ID 1)
        User::create([
            'name' => 'Admin User',
            'role' => 'admin',
            'email' => 'admin@example.com',
            'google_id' => 'google-admin-001',
            'nomor_hp' => '081234567890',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'status' => 'aktif',
            'keterangan' => 'Akun administrator utama',
        ]);

        // 2. Membuat User Member (ID 2)
        User::create([
            'name' => 'Member User',
            'role' => 'member',
            'email' => 'member@example.com',
            'google_id' => 'google-member-001',
            'nomor_hp' => '081223344556',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'status' => 'aktif',
            'keterangan' => 'Akun member terdaftar',
        ]);

        // 3. Membuat 5 User Kasir Khusus (ID 3, 4, 5, 6, 7)
        $kasirUsers = [];
        for ($i = 1; $i <= 5; $i++) {
            $kasirUsers[] = [
                'name' => "Kasir Warung {$i}",
                'role' => 'kasir',
                'email' => "kasir{$i}@example.com",
                'google_id' => "google-kasir-00{$i}",
                'nomor_hp' => '0877777777' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'status' => 'aktif',
                'keterangan' => "Akun kasir untuk Warung {$i}",
            ];
        }
        User::insert($kasirUsers);

        // 4. Membuat beberapa user dummy
        User::factory()->count(10)->create();
    }
}
