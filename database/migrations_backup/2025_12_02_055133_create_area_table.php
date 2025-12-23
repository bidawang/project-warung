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
        Schema::create('area', function (Blueprint $table) {
            // Kolom 'id'
            // MariaDB: bigint(20) unsigned, NO, PRI, auto_increment
            $table->id(); 

            // Kolom 'area'
            // MariaDB: varchar(100), NO, UNI
            $table->string('area', 100)->unique(); 
            
            // Kolom 'keterangan'
            // MariaDB: text, YES (Nullable)
            $table->text('keterangan')->nullable(); 
            
            // Kolom 'created_at' dan 'updated_at'
            // MariaDB: timestamp, YES (Nullable)
            // Catatan: Laravel 'timestamps()' secara default menggunakan tipe data TIMESTAMP NULLABLE.
            // Jika Anda ingin *exact* behavior seperti di tabel Anda (TIMESTAMP NULL), 
            // fungsi timestamps() sudah melakukan hal tersebut.
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area');
    }
};