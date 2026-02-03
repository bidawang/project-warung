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
        Schema::create('rencana_belanja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('warung')->onDelete('cascade');
            $table->foreignId('id_barang')->constrained('barang')->onDelete('cascade');
            $table->unsignedInteger('jumlah_awal');
            $table->unsignedInteger('jumlah_dibeli')->default(0);
            $table->unsignedInteger('jumlah_diterima');
            $table->enum('status', ['pending', 'dibeli', 'dikirim', 'selesai', 'batal'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rencana_belanja');
    }
};
