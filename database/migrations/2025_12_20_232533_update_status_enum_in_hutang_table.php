<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 2. Ubah struktur kolom ENUM
        DB::statement("ALTER TABLE hutang MODIFY COLUMN status ENUM('lunas', 'belum_lunas', 'lewat_tenggat') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke format lama (dengan spasi)
        DB::statement("ALTER TABLE hutang MODIFY COLUMN status ENUM('lunas', 'belum lunas', 'lewat_tenggat') NOT NULL");

        DB::table('hutang')
            ->where('status', 'belum_lunas')
            ->update(['status' => 'belum lunas']);
    }
};
