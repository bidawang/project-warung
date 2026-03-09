<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('warung')->cascadeOnDelete();
            $table->string('nama');
            $table->decimal('harga_asset', 15, 2);
            $table->date('tanggal_pembelian');
            $table->decimal('total_dibayar', 15, 2)->default(0);
            $table->decimal('sisa_pembayaran', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset');
    }
};