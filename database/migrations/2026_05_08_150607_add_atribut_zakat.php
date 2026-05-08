<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('warung', function (Blueprint $table) {
            // Menggunakan decimal untuk akurasi nilai uang
            // 15 total digit, 2 digit di belakang koma
            $table->Integer('zakat')->after('pembagian_laba')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warung', function (Blueprint $table) {
            $table->dropColumn(['zakat']);
        });
    }
};
