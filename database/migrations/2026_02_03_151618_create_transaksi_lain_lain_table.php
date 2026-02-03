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
        Schema::create('transaksi_lain_lain', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_transaksi_awal')->nullable()->constrained('transaksi_awal');
            $table->text('keterangan')->nullable();
            $table->decimal('harga', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_lain_lain');
    }
};
