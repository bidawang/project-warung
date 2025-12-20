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
        Schema::create('area_pembelian', function (Blueprint $table) {
            // Kolom 'id'
            // MariaDB: bigint(20) unsigned, NO, PRI, auto_increment
            $table->id(); 
            
            // Kolom 'area'
            // MariaDB: varchar(255), NO (NOT NULL)
            $table->string('area'); 
            
            // Kolom 'markup'
            // MariaDB: decimal(8,2), NO (NOT NULL)
            $table->decimal('markup', 8, 2); 
            
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
        Schema::dropIfExists('area_pembelian');
    }
};