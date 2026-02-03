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
        Schema::create('area_pembelian', function (Blueprint $table) {
            $table->id(); // Ini akan membuat kolom 'id' sebagai primary key.
            $table->string('area'); // Ini akan membuat kolom 'area' dengan tipe data string.
            $table->decimal('markup', 8, 2); // Ini akan membuat kolom 'markup' dengan tipe data decimal, 8 digit total dan 2 digit di belakang koma.
            $table->timestamps(); // Ini akan otomatis membuat kolom 'created_at' dan 'updated_at'.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_pembelian');
    }
};