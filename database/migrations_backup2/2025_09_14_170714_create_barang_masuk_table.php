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
            $table->unsignedBigInteger('id_transaksi_barang');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_stok_warung');
            $table->integer('jumlah');
            $table->string('keterangan')->nullable();
            $table->enum('status', ['pending', 'kirim', 'terima', 'tolak'])->default('pending');
            $table->enum('jenis', ['rencana', 'tambahan']);
            $table->dateTime('tanggal_kadaluarsa')->nullable();
            $table->timestamps();

            $table->foreign('id_transaksi_barang')->references('id')->on('transaksi_barang')->onDelete('cascade');
            $table->foreign('id_barang')->references('id')->on('barang')->onDelete('cascade');
            $table->foreign('id_stok_warung')->references('id')->on('stok_warung')->onDelete('cascade');
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
