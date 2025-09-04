<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_mutasi_barangs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')->constrained('barang');
            $table->foreignId('warung_asal')->constrained('warung');
            $table->foreignId('warung_tujuan')->constrained('warung');
            $table->integer('jumlah');
            $table->string('status', 50);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_barang');
    }
};