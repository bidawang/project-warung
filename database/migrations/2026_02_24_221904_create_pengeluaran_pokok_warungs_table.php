<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluaran_pokok_warung', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_warung');
            $table->unsignedBigInteger('id_transaksi_kas')->nullable();
            $table->string('redaksi');
            $table->decimal('jumlah', 15, 2);
            $table->date('date');
            $table->enum('status',['terpenuhi','belum terpenuhi'])->default('belum terpenuhi');
            $table->timestamps();

            // Optional foreign key
            $table->foreign('id_warung')
                ->references('id')
                ->on('warung')
                ->onDelete('cascade');
            $table->foreign('id_transaksi_kas')
                ->references('id')
                ->on('transaksi_kas')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_pokok_warung');
    }
};
