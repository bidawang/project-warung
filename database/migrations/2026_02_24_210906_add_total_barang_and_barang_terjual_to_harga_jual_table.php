<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('harga_jual', function (Blueprint $table) {
            $table->integer('total_barang')->default(0)->after('harga_jual_range_akhir');
            $table->integer('barang_terjual')->default(0)->after('total_barang');
        });
    }

    public function down(): void
    {
        Schema::table('harga_jual', function (Blueprint $table) {
            $table->dropColumn(['total_barang', 'barang_terjual']);
        });
    }
};
