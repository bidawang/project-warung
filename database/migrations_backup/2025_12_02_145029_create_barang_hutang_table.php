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
        Schema::create('barang_hutang', function (Blueprint $table) {
            // Kolom 'id' (Primary Key)
            $table->id(); 

            // Foreign Key 'id_hutang'
            // Menghubungkan ke tabel 'hutang'
            $table->foreignId('id_hutang')
                  ->constrained('hutang') 
                  ->onUpdate('cascade') 
                  ->onDelete('cascade'); // Jika hutang dihapus, item ini juga dihapus
            
            // Foreign Key 'id_barang_keluar'
            // Menghubungkan ke tabel 'barang_keluar'
            $table->foreignId('id_barang_keluar')
                  ->constrained('barang_keluar')
                  ->onUpdate('cascade') 
                  ->onDelete('cascade'); // Jika barang keluar dibatalkan, item ini juga dihapus

            // (Opsional, tapi sering digunakan pada tabel pivot untuk mencegah duplikasi)
            // $table->unique(['id_hutang', 'id_barang_keluar']);

            // Kolom 'created_at' dan 'updated_at'
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_hutang');
    }
};