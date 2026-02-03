<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_sub_kategoris_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_kategori', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kategori')->constrained('kategori');
            $table->string('sub_kategori', 100);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_kategori');
    }
};