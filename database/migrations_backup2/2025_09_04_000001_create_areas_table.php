<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_areas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area', function (Blueprint $table) {
            $table->id();
            $table->string('area', 100)->unique();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area');
    }
};
