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
        Schema::create('barang_hutang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_hutang');
            $table->unsignedBigInteger('id_barang_keluar');
            $table->timestamps();

            // Menambahkan kunci asing
            $table->foreign('id_hutang')->references('id')->on('hutang')->onDelete('cascade');
            $table->foreign('id_barang_keluar')->references('id')->on('barang_keluar')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_hutang');
    }
};
