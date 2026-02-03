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
        Schema::create('transaksi_kas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kas_warung')->constrained('kas_warung');
            $table->decimal('total', 15, 2);
            $table->string('metode_pembayaran', 50)->nullable();
            $table->enum('jenis', [
                'penjualan barang',
                'penjualan pulsa',
                'hutang barang',
                'hutang pulsa',
                'expayet',
                'hilang',
                'masuk',
                'keluar',
                'opname -',
                'opname +',
                'inject'
            ]);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_kas');
    }
};
