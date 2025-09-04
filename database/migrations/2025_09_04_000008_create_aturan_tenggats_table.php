<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_aturan_tenggats_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aturan_tenggat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_area')->constrained('area');
            $table->date('tanggal_awal');
            $table->date('tanggal_akhir');
            $table->integer('jatuh_tempo_hari');
            $table->integer('jatuh_tempo_bulan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aturan_tenggat');
    }
};