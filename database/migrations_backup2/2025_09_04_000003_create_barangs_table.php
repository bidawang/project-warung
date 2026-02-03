<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_barangs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sub_kategori')->constrained('sub_kategori');
            $table->string('kode_barang', 50)->unique();
            $table->string('nama_barang', 100);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};