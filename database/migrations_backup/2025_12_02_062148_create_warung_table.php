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
        Schema::create('warung', function (Blueprint $table) {
            // Kolom 'id'
            // MariaDB: bigint(20) unsigned, NO, PRI, auto_increment
            $table->id();

            // Foreign Key 'id_user'
            // MariaDB: bigint(20) unsigned, NO, MUL (Index)
            $table->foreignId('id_user')
                  ->constrained('users') // Menghubungkan ke tabel 'users'
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // Foreign Key 'id_area'
            // MariaDB: bigint(20) unsigned, NO, MUL (Index)
            $table->foreignId('id_area')
                  ->constrained('area') // Menghubungkan ke tabel 'area'
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // Kolom Data Warung
            // MariaDB: varchar(100), NO (NOT NULL)
            $table->string('nama_warung', 100);

            // Kolom 'modal'
            // MariaDB: decimal(15,2), NO (NOT NULL)
            $table->decimal('modal', 15, 2);

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
        Schema::dropIfExists('warung');
    }
};
