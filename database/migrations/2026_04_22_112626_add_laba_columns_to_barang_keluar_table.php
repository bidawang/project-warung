<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            // Menggunakan decimal untuk akurasi nilai uang
            // 15 total digit, 2 digit di belakang koma
            $table->decimal('laba_owner', 15, 2)->after('laba_bersih')->default(0);
            $table->decimal('laba_warung', 15, 2)->after('laba_owner')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropColumn(['laba_owner', 'laba_warung']);
        });
    }
};
