<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Kasir;
use App\Models\Member;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Membuat User Admin secara manual
        User::create([
            'name' => 'Admin User',
            'role' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'keterangan' => 'Akun administrator utama',
        ]);

        // 2. Membuat User Kasir dan entri di tabel `kasir`
        $kasirUser = User::create([
            'name' => 'Kasir User',
            'role' => 'kasir',
            'email' => 'kasir@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'keterangan' => 'Akun kasir untuk transaksi',
        ]);
        Kasir::create([
            'id_user' => $kasirUser->id,
            'keterangan' => 'Data kasir untuk user ' . $kasirUser->name,
        ]);

        // 3. Membuat User Member dan entri di tabel `member`
        $memberUser = User::create([
            'name' => 'Member User',
            'role' => 'member',
            'email' => 'member@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'keterangan' => 'Akun member terdaftar',
        ]);
        Member::create([
            'id_user' => $memberUser->id,
            'kode_user' => 'MEMBER-' . time(), // Contoh kode unik
            'keterangan' => 'Data member untuk user ' . $memberUser->name,
        ]);

        // 4. Membuat 10 user dummy (5 kasir dan 5 member)
        $users = User::factory()->count(10)->create();

        foreach ($users as $user) {
            if ($user->role === 'kasir') {
                Kasir::create([
                    'id_user' => $user->id,
                    'keterangan' => 'Data kasir dari factory',
                ]);
            } elseif ($user->role === 'member') {
                Member::create([
                    'id_user' => $user->id,
                    'kode_user' => 'MEMBER-' . time() . '-' . $user->id,
                    'keterangan' => 'Data member dari factory',
                ]);
            }
        }
    }
}
