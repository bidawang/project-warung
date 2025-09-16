<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_stok_warung')->constrained('stok_warung');
            $table->foreignId('warung_asal')->constrained('warung');
            $table->foreignId('warung_tujuan')->constrained('warung');
            $table->foreignId('id_barang_masuk')->constrained('barang_masuk');
            $table->integer('jumlah');
            $table->enum('status', ['pending', 'terima', 'tolak'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_barang');
    }
};
