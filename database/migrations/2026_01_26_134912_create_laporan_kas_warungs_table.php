<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laporan_kas_warung', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment
            $table->unsignedBigInteger('id_kas_warung');
            $table->decimal('pecahan', 15, 2);
            $table->integer('jumlah');
            $table->enum('tipe', ['laporan', 'adjustment']);
            $table->timestamps();

            // kalau id_kas_warung relasi ke kas_warung
            $table->foreign('id_kas_warung')
                ->references('id')
                ->on('kas_warung')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kas_warung');
    }
};
