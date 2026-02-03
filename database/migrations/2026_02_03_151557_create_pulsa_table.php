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
            $table->foreignId('id_warung')->constrained('warung');
            $table->foreignId('jenis_pulsa_id')->constrained('jenis_pulsa');
            $table->decimal('harga_pulsa', 10, 2)->nullable();
            $table->decimal('saldo', 10, 2);
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
