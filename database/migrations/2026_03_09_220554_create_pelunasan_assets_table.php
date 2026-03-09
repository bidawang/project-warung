<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelunasan_asset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_asset')->constrained('asset')->cascadeOnDelete();
            $table->decimal('jumlah_bayar', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelunasan_asset');
    }
};