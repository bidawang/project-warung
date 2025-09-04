<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_hutang_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hutang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('warung');
            $table->foreignId('id_user')->constrained('users');
            $table->decimal('jumlah', 15, 2);
            $table->date('tenggat');
            $table->enum('status', ['lunas', 'belum lunas']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hutang');
    }
};
