<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_bunga_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bunga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_hutang')->constrained('hutang');
            $table->decimal('bunga', 8, 2);
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bunga');
    }
};
