<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kuantitas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_laba');
            $table->integer('jumlah');
            $table->bigInteger('harga_jual')();
            $table->timestamps();

            $table->foreign('id_laba')->references('id')->on('labas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kuantitas');
    }
};