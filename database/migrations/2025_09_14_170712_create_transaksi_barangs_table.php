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
            $table->foreignId('id_barang')->constrained('barang');
            $table->foreignId('id_area_pembelian')->constrained('area_pembelian');
            $table->foreignId('id_transaksi_awal')->constrained('transaksi_awal');
            $table->integer('jumlah');
            $table->integer('jumlah_terpakai')->default(0);
            $table->enum('status', ['pending', 'selesai', 'dikirim', 'terima', 'tolak'])->default('pending');
            $table->decimal('harga', 15, 2);
            $table->enum('jenis', ['rencana', 'tambahan']);
            $table->date('tanggal_kadaluarsa')->nullable(); // Menambahkan kolom baru
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_barang');
    }
};
