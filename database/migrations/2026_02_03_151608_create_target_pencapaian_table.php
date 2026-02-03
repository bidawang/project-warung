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
        Schema::create('target_pencapaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('warung');
            $table->date('periode_awal');
            $table->date('periode_akhir');
            $table->decimal('tercapai', 15, 2);
            $table->decimal('target_pencapaian', 15, 2);
            $table->string('status_pencapaian', 50);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_pencapaian');
    }
};
