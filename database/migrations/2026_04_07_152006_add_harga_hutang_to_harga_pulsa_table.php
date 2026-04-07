<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('harga_pulsa', function (Blueprint $table) {
            // Menambahkan kolom harga_hutang setelah kolom harga
            $table->decimal('harga_hutang', 15, 2)->default(0)->after('harga');
        });
    }

    public function down(): void
    {
        Schema::table('harga_pulsa', function (Blueprint $table) {
            $table->dropColumn('harga_hutang');
        });
    }
};
