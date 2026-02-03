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
        Schema::create('stok_opname', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_stok_warung')->constrained('stok_warung')->onDelete('cascade');
            $table->integer('jumlah_sebelum');
            $table->integer('jumlah_sesudah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_opname');
    }
};
