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
        // 1. Membuat User Admin
        User::create([
            'name' => 'Admin User',
            'role' => 'admin',
            'email' => 'admin@example.com',
            'google_id' => 'google-admin-001', // contoh isi wajib unik
            'nomor_hp' => '081234567890',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'status' => 'aktif',
            'keterangan' => 'Akun administrator utama',
        ]);

        // 2. Membuat User Kasir (jika role masih dipakai, tapi tidak ada tabel kasir)
        User::create([
            'name' => 'Kasir User',
            'role' => 'kasir',
            'email' => 'kasir@example.com',
            'google_id' => 'google-kasir-001',
            'nomor_hp' => '081298765432',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'status' => 'aktif',
            'keterangan' => 'Akun kasir untuk transaksi',
        ]);

        // 3. Membuat User Member
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

        // 4. Membuat beberapa user dummy
        User::factory()->count(10)->create();
    }
}
