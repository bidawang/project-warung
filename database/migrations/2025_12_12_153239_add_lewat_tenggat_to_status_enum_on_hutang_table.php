<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE hutang MODIFY COLUMN status ENUM('lunas', 'belum lunas', 'lewat_tenggat') NOT NULL;");
    }

    public function down(): void
    {
        // Kembalikan ke enum awal (tanpa lewat_tenggat)
        DB::statement("ALTER TABLE hutang MODIFY COLUMN status ENUM('lunas', 'belum lunas') NOT NULL;");
    }
};

