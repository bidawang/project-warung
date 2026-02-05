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
        Schema::create('hutang_warung', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('warung')->onDelete('cascade');
            // Menggunakan decimal untuk akurasi nilai uang (15 digit total, 2 di belakang koma)
            $table->decimal('total', 15, 2)->default(0);
            // Enum untuk kategori transaksi
            $table->enum('jenis', ['barang masuk', 'opname', 'inject']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hutang_warung');
    }
};
