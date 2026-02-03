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
        Schema::create('transaksi_barang_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_transaksi_kas')->constrained('transaksi_kas');
            $table->foreignId('id_barang_keluar')->constrained('barang_keluar');
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_barang_keluar');
    }
};
