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
        Schema::create('hutang', function (Blueprint $table) {
            // Kolom 'id' (Primary Key)
            $table->id(); 

            // Foreign Key 'id_warung'
            // Menghubungkan ke tabel 'warung'
            $table->foreignId('id_warung')
                  ->constrained('warung') 
                  ->onUpdate('cascade') 
                  ->onDelete('restrict'); 
            
            // Foreign Key 'id_user' (Pencatat/Penanggung Jawab Hutang)
            // Menghubungkan ke tabel 'users'
            $table->foreignId('id_user')
                  ->constrained('users')
                  ->onUpdate('cascade') 
                  ->onDelete('restrict'); 

            // Kolom Jumlah Hutang
            // MariaDB: decimal(15,2), NO (NOT NULL)
            $table->decimal('jumlah_hutang_awal', 15, 2); 
            $table->decimal('jumlah_sisa_hutang', 15, 2); 
            
            // Kolom 'tenggat'
            // MariaDB: date, NO (NOT NULL)
            $table->date('tenggat'); 
            
            // Kolom 'status'
            // MariaDB: enum('lunas','belum lunas'), NO (NOT NULL)
            $table->enum('status', ['lunas', 'belum lunas']); 
            
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
        Schema::dropIfExists('hutang');
    }
};