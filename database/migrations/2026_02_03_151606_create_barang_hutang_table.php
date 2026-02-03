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
            $table->foreignId('id_hutang')->constrained('hutang')->onDelete('cascade');
            $table->foreignId('id_barang_keluar')->constrained('barang_keluar')->onDelete('cascade');
            $table->timestamps();
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
