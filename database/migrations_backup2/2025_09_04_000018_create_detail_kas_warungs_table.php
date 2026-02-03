<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_detail_kas_warungs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_kas_warung', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kas_warung')->constrained('kas_warung');
            $table->decimal('pecahan', 15, 2);
            $table->integer('jumlah');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_kas_warung');
    }
};