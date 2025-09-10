<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kuantitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_stok_warung')->constrained('stok_warung');
            $table->integer('jumlah');
            $table->decimal('harga_jual', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kuantitas');
    }
};
