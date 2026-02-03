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
        Schema::create('hutang_opname', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_hutang_barang_masuk')->constrained('hutang_barang_masuk')->onDelete('cascade');
            $table->foreignId('id_stok_opname')->constrained('stok_opname')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hutang_opname');
    }
};
