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
            // Kita gunakan decimal untuk nilai uang agar presisi
            // Kita letakkan setelah kolom 'harga_jual'
            $table->decimal('laba_bersih', 15, 2)->default(0)->after('harga_jual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropColumn('laba_bersih');
        });
    }
};