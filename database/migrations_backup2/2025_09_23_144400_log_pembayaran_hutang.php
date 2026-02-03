<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
    {
        Schema::create('log_pembayaran_hutang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_hutang')->constrained('hutang')->onDelete('cascade');
            $table->decimal('jumlah_pembayaran', 15, 2);
            $table->dateTime('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_pembayaran_hutang');
    }
};
