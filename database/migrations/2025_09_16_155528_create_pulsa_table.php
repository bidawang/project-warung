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
        Schema::create('pulsa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('warung'); // Foreign key ke tabel 'warung'
            $table->foreignId('id_harga_pulsa')->constrained('harga_pulsa'); // Foreign key ke tabel 'harga_pulsa'
            $table->integer('saldo'); // Menyimpan jumlah stok yang tersedia
            $table->enum('jenis', ['hp', 'listrik']); // Tipe pulsa, terbatas pada 'hp' atau 'listrik'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pulsa');
    }
};
