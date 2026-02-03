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
        Schema::create('barang_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_stok_warung')->constrained('stok_warung');
            $table->integer('jumlah');
            $table->decimal('harga_jual', 15, 2)->default(0.00);
            $table->enum('jenis', ['penjualan barang', 'hutang barang', 'expayet', 'hilang', 'opname -', 'opname +', 'mutasi'])->default('penjualan barang');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_keluar');
    }
};
