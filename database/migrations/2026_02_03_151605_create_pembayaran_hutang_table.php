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
        Schema::create('pembayaran_hutang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_transaksi_kas')->constrained('transaksi_kas');
            $table->foreignId('id_hutang')->constrained('hutang');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_hutang');
    }
};
