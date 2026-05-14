<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('saldo_pulsa', function (Blueprint $col) {
            $col->id();
            $col->unsignedBigInteger('id_warung');
            $col->unsignedBigInteger('id_jenis');
            $col->decimal('jumlah', 15, 2)->default(0); // Sesuai kolom "Jumlah" di gambar
            $col->timestamps();

            $col->foreign('id_warung')->references('id')->on('warungs')->onDelete('cascade');
            $col->foreign('id_jenis')->references('id')->on('jenis_pulsa')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saldo_pulsa');
    }
};
