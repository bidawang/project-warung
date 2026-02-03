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
        Schema::create('harga_jual', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('warung')->onDelete('cascade');
            $table->foreignId('id_barang')->constrained('barang')->onDelete('cascade');
            $table->decimal('harga_sebelum_markup', 15, 2);
            $table->decimal('harga_modal', 15, 2);
            $table->decimal('harga_jual_range_awal', 15, 2);
            $table->decimal('harga_jual_range_akhir', 15, 2);
            $table->date('periode_awal');
            $table->date('periode_akhir')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_jual');
    }
};
