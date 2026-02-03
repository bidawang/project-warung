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
        Schema::create('harga_pulsa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_pulsa_id')->nullable()->constrained('jenis_pulsa')->onDelete('cascade');
            $table->decimal('jumlah_pulsa', 10, 0);
            $table->decimal('harga', 10, 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_pulsa');
    }
};
