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
            // Kolom 'id' (Primary Key)
            $table->id();

            // Foreign Key 'id_stok_warung'
            // Menghubungkan ke tabel 'stok_warung' (Asumsi tabel ini sudah atau akan dibuat)
            $table->foreignId('id_stok_warung')
                  ->constrained('stok_warung') // Ganti dengan nama tabel stok yang benar jika berbeda
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // Kolom 'jumlah'
            // MariaDB: int(11), NO (NOT NULL)
            $table->integer('jumlah');

            // Kolom 'jenis' (ENUM)
            // MariaDB: enum('penjualan barang','hutang barang','expayet','hilang','opname -','opname +'), Default 'penjualan barang'
            $table->enum('jenis', [
                'penjualan barang',
                'hutang barang',
                'expayet',
                'hilang',
                'opname -', // Pengurangan stok karena Opname
                'opname +'  // Penambahan stok karena Opname (Walaupun ini barang_KELUAR, ini mungkin untuk koreksi)
            ])->default('penjualan barang');

            // Kolom 'keterangan'
            $table->text('keterangan')->nullable();

            // Kolom 'created_at' dan 'updated_at'
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
