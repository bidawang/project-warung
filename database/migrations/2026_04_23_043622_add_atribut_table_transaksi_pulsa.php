<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_pulsa', function (Blueprint $table) {
            // Menggunakan decimal untuk akurasi nilai uang
            // 15 total digit, 2 digit di belakang koma
            $table->decimal('laba_pulsa', 15, 2)->default(0);
            $table->decimal('laba_owner', 15, 2)->default(0);
            $table->decimal('laba_warung', 15, 2)->default(0);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_pulsa', function (Blueprint $table) {
            $table->dropColumn(['laba_pulsa', 'laba_owner', 'laba_warung']);
        });
    }
};
