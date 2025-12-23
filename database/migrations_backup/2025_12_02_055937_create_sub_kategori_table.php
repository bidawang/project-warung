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
        Schema::create('sub_kategori', function (Blueprint $table) {
            // Kolom 'id'
            // MariaDB: bigint(20) unsigned, NO, PRI, auto_increment
            $table->id();

            // Kolom 'id_kategori' (Foreign Key)
            // MariaDB: bigint(20) unsigned, NO, MUL (Index)
            // Relasi ke tabel 'kategori'
            $table->foreignId('id_kategori')
                  ->constrained('kategori') // Menghubungkan ke tabel 'kategori'
                  ->onUpdate('cascade')    // Opsi: Update di kategori akan mengupdate di sini
                  ->onDelete('cascade');   // Opsi: Hapus kategori akan menghapus sub_kategori terkait

            // Kolom 'sub_kategori'
            // MariaDB: varchar(100), NO (NOT NULL)
            $table->string('sub_kategori', 100);

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
        Schema::dropIfExists('sub_kategori');
    }
};
