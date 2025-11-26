<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asal_barang', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel barang
            $table->unsignedBigInteger('id_barang');
            $table->foreign('id_barang')
                  ->references('id')->on('barang')
                  ->onDelete('cascade');

            // Relasi ke tabel area_pembelian
            $table->unsignedBigInteger('id_area_pembelian');
            $table->foreign('id_area_pembelian')
                  ->references('id')->on('area_pembelian')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asal_barang');
    }
};
