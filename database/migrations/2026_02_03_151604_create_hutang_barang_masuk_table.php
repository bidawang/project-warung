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
        Schema::create('hutang_barang_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('warung')->onDelete('cascade');
            $table->foreignId('id_barang_masuk')->nullable()->constrained('barang_masuk')->onDelete('cascade');
            $table->integer('total');
            $table->enum('status', ['lunas', 'belum lunas']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hutang_barang_masuk');
    }
};
