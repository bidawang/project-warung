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
        Schema::create('harga_pulsa', function (Blueprint $table) {
            $table->id(); // Membuat kolom 'id' sebagai primary key
            $table->decimal('jumlah_pulsa', 10, 0); // Menyimpan jumlah nominal pulsa (misal: 5000)
            $table->decimal('harga', 10, 0); // Menyimpan harga jual pulsa
            $table->timestamps(); // Menambahkan kolom 'created_at' dan 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_pulsa');
    }
};
