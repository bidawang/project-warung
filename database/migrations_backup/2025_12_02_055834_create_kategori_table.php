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
        Schema::create('kategori', function (Blueprint $table) {
            // Kolom 'id'
            // MariaDB: bigint(20) unsigned, NO, PRI, auto_increment
            $table->id();

            // Kolom 'kategori'
            // MariaDB: varchar(100), NO, UNI
            $table->string('kategori', 100)->unique();

            // Kolom 'keterangan'
            // MariaDB: text, YES (Nullable)
            $table->text('keterangan')->nullable();

            // Kolom 'created_at' dan 'updated_at'
            // MariaDB: timestamp, YES (Nullable)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};
