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
        Schema::create('laba', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_area')->constrained('area');
            $table->integer('input_minimal');
            $table->integer('input_maksimal');
            $table->decimal('harga_jual', 15, 2);
            $table->string('jenis', 50);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laba');
    }
};
