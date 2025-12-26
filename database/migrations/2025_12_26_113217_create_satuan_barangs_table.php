<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('satuan_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')
                ->constrained('barang')
                ->cascadeOnDelete();

            $table->foreignId('id_satuan')
                ->constrained('satuan')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['id_barang', 'id_satuan']); // anti dobel, hidup lebih tenang
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satuan_barang');
    }
};
