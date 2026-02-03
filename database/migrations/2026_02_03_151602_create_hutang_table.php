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
        Schema::create('hutang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warung')->constrained('warung');
            $table->foreignId('id_user')->constrained('users');
            $table->decimal('jumlah_hutang_awal', 15, 2);
            $table->decimal('jumlah_sisa_hutang', 15, 2);
            $table->date('tenggat');
            $table->enum('status', ['lunas', 'belum lunas']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hutang');
    }
};
