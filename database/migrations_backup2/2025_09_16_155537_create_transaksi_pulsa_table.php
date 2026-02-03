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
            $table->foreignId('id_pulsa')->constrained('pulsa'); // Foreign key ke tabel 'pulsa'
            $table->foreignId('id_kas_warung')->constrained('kas_warung'); // Foreign key ke tabel 'kas_warung'
            $table->integer('jumlah'); // Jumlah pulsa yang ditransaksikan
            $table->integer('total'); // Jumlah pulsa yang ditransaksikan
            $table->enum('jenis_pembayaran', ['tunai', 'non_tunai']); // Jumlah pulsa yang ditransaksikan
            $table->enum('jenis', ['keluar', 'masuk']); // Tipe transaksi: 'keluar' (penjualan) atau 'masuk' (pembelian)
            $table->string('tipe'); // Tipe pembayaran atau transaksi, misal: 'tunai'
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
