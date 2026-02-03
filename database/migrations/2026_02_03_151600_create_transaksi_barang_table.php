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
        Schema::create('transaksi_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')->constrained('barang');
            $table->foreignId('id_area_pembelian')->constrained('area_pembelian');
            $table->foreignId('id_transaksi_awal')->constrained('transaksi_awal');
            $table->integer('jumlah');
            $table->integer('jumlah_terpakai');
            $table->enum('status', ['pending', 'selesai', 'dikirim', 'terima', 'tolak', 'parsial'])->default('pending');
            $table->enum('jenis', ['rencana', 'tambahan'])->nullable();
            $table->decimal('harga', 15, 2);
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_barang');
    }
};
