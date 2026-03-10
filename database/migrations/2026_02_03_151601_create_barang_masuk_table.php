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
        Schema::create('barang_masuk', function (Blueprint $table) {
            $table->id();
            // 1. Buat kolomnya dulu
            $table->unsignedBigInteger('id_transaksi_barang_masuk');

            // 2. Definisi constraint foreign key-nya
            $table->foreign('id_transaksi_barang_masuk', 'fk_barang_masuk_transaksi') // 'fk_...' adalah nama index (opsional)
                ->references('id')                 // Kolom yang dirujuk di tabel induk
                ->on('transaksi_barang_masuk')     // Nama tabel induk
                ->onDelete('cascade');             // Aksi saat data dihapus            $table->foreignId('id_barang')->constrained('barang')->onDelete('cascade');
            $table->foreignId('id_stok_warung')->constrained('stok_warung')->onDelete('cascade');
            $table->integer('jumlah');
            $table->string('keterangan', 255)->nullable();
            $table->enum('status', ['pending', 'kirim', 'terima', 'tolak'])->default('pending');
            $table->enum('jenis', ['rencana', 'tambahan']);
            $table->datetime('tanggal_kadaluarsa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_masuk');
    }
};
