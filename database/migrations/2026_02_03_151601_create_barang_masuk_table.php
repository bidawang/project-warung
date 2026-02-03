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
        Schema::create('barang_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_transaksi_barang')->constrained('transaksi_barang')->onDelete('cascade');
            $table->foreignId('id_barang')->constrained('barang')->onDelete('cascade');
            $table->foreignId('id_stok_warung')->constrained('stok_warung')->onDelete('cascade');
            $table->integer('jumlah');
            $table->string('keterangan', 255)->nullable();
            $table->enum('status', ['pending', 'kirim', 'terima', 'tolak'])->default('pending');
            $table->enum('jenis', ['rencana', 'tambahan']);
            $table->datetime('tanggal_kadaluarsa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_masuk');
    }
};
