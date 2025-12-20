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
        Schema::create('asal_barang', function (Blueprint $table) {
            // Kolom 'id'
            // MariaDB: bigint(20) unsigned, NO, PRI, auto_increment
            $table->id();

            // Kolom 'id_barang' (Foreign Key)
            // MariaDB: bigint(20) unsigned, NO, MUL (Index)
            $table->foreignId('id_barang')
                  ->constrained('barang') // Menghubungkan ke tabel 'barang'
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // Kolom 'id_area_pembelian' (Foreign Key)
            // MariaDB: bigint(20) unsigned, NO, MUL (Index)
            $table->foreignId('id_area_pembelian')
                  ->constrained('area_pembelian') // Menghubungkan ke tabel 'area_pembelian'
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // Kolom 'created_at' dan 'updated_at'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asal_barang');
    }
};
