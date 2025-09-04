<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_transaksi_kas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kas_warung')->constrained('kas_warung');
            $table->decimal('total', 15, 2);
            $table->string('metode_pembayaran', 50);
            $table->text('keterangan')->nullable();
            $table->enum('jenis', ['keluar', 'masuk']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_kas');
    }
};