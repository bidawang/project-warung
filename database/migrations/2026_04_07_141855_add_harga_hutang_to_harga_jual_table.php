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
        Schema::table('harga_jual', function (Blueprint $table) {
            // Menambahkan kolom harga_hutang setelah harga_jual_range_akhir
            // Menggunakan decimal(15,2) agar presisi untuk data uang
            $table->decimal('harga_hutang', 15, 2)->nullable()->after('harga_jual_range_akhir')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('harga_jual', function (Blueprint $table) {
            $table->dropColumn('harga_hutang');
        });
    }
};
