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
        Schema::create('rencana_belanja', function (Blueprint $table) {
            $table->id(); // Primary Key

            // Kolom untuk Foreign Key ke tabel 'warung'
            $table->foreignId('id_warung')
                ->constrained('warung') // Asumsi nama tabel adalah 'warung'
                ->onDelete('cascade');

            // Kolom untuk Foreign Key ke tabel 'barang'
            $table->foreignId('id_barang')
                ->constrained('barang') // Asumsi nama tabel adalah 'barang'
                ->onDelete('cascade');

            // Kolom jumlah yang direncanakan
            $table->unsignedInteger('jumlah_awal');

            // Kolom jumlah yang sudah dibeli (default 0)
            $table->unsignedInteger('jumlah_dibeli')->default(0);
            $table->unsignedInteger('jumlah_diterima')->default(0);
            // STATUS
            $table->enum('status', ['pending', 'dibeli', 'dikirim', 'selesai', 'batal'])
                ->default('pending');

            // Kolom timestamps (created_at dan updated_at)
            $table->timestamps();

            // Opsional: Menetapkan kombinasi Warung dan Barang harus unik
            // $table->unique(['id_warung', 'id_barang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rencana_belanja');
    }
};
