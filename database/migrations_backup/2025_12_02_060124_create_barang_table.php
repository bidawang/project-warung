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
        Schema::create('barang', function (Blueprint $table) {
            // Kolom 'id'
            // MariaDB: bigint(20) unsigned, NO, PRI, auto_increment
            $table->id();

            // Kolom 'id_sub_kategori' (Foreign Key)
            // MariaDB: bigint(20) unsigned, NO, MUL (Index)
            $table->foreignId('id_sub_kategori')
                  ->constrained('sub_kategori') // Menghubungkan ke tabel 'sub_kategori'
                  ->onUpdate('cascade')
                  ->onDelete('restrict'); // Disarankan menggunakan 'restrict' untuk data produk utama

            // Kolom 'kode_barang'
            // MariaDB: varchar(50), NO, UNI (Unique)
            $table->string('kode_barang', 50)->unique();

            // Kolom 'nama_barang'
            // MariaDB: varchar(100), NO (NOT NULL)
            $table->string('nama_barang', 100);

            // Kolom 'keterangan'
            // MariaDB: text, YES (Nullable)
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
        Schema::dropIfExists('barang');
    }
};
