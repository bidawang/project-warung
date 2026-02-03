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
        Schema::create('hutang_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_hutang_barang_masuk')
                ->constrained('hutang_barang_masuk')
                ->cascadeOnDelete();

            $table->foreignId('id_stok_opname')
                ->constrained('stok_opname')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hutang_opnames');
    }
};
