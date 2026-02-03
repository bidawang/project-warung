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
        Schema::create('transaksi_pulsa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pulsa')->constrained('pulsa');
            $table->foreignId('id_kas_warung')->nullable()->constrained('kas_warung');
            $table->integer('jumlah');
            $table->integer('total');
            $table->enum('jenis_pembayaran', ['tunai', 'non_tunai']);
            $table->enum('jenis', ['keluar', 'masuk']);
            $table->string('tipe', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_pulsa');
    }
};
