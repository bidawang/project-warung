<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_transaksi_barangs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_transaksi_kas')->constrained('transaksi_kas');
            $table->foreignId('id_barang')->constrained('barang');
            $table->integer('jumlah');
            $table->string('status', 50);
            $table->enum('jenis', ['keluar', 'masuk']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_barang');
    }
};