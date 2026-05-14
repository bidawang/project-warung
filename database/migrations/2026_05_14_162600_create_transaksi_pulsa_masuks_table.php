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
        Schema::create('transaksi_pulsa_masuk', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_pulsa');
            $table->unsignedBigInteger('id_hutang_warung');

            $table->integer('jumlah');
            $table->bigInteger('harga_alomogada');
            $table->bigInteger('total');

            $table->timestamps();

            // Foreign Key
            $table->foreign('id_pulsa')
                ->references('id')
                ->on('pulsa')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('id_hutang_warung')
                ->references('id')
                ->on('hutang_warung')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_pulsa_masuk');
    }
};
