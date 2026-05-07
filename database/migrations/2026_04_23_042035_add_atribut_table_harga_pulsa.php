<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('harga_pulsa', function (Blueprint $table) {
            // Menggunakan decimal untuk akurasi nilai uang
            // 15 total digit, 2 digit di belakang koma
            $table->decimal('harga_alomogada', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->dropColumn('harga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('harga_pulsa', function (Blueprint $table) {
            $table->dropColumn(['harga_almomogada', 'harga_jual','harga']);
        });
    }
};
