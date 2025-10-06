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
            $table->decimal('harga_pulsa', 10, 2); // Foreign key ke tabel 'harga_pulsa'
            $table->decimal('saldo', 10, 2); // Menyimpan jumlah stok yang tersedia
            $table->enum('jenis', ['hp', 'listrik']); // Tipe pulsa, terbatas pada 'hp' atau 'listrik'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pulsa');
    }
};
