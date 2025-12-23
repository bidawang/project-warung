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
        Schema::create('aturan_tenggat', function (Blueprint $table) {
            // Kolom 'id'
            $table->id();

            // Foreign Key 'id_warung'
            // Menghubungkan ke tabel 'warung'
            $table->foreignId('id_warung')
                  ->constrained('warung')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // Kolom Aturan Tanggal/Hari
            // MariaDB: int(11), NO (NOT NULL)
            $table->integer('tanggal_awal');
            $table->integer('tanggal_akhir');
            $table->integer('jatuh_tempo_hari');

            // Kolom 'bunga' (Disimpan sebagai VARCHAR)
            // MariaDB: varchar(50), NO (NOT NULL)
            // Catatan: Jika 'bunga' adalah persentase, sebaiknya gunakan $table->decimal atau $table->float.
            // Namun, mengikuti skema Anda, kita gunakan string/varchar.
            $table->string('bunga', 50);

            // Kolom 'keterangan'
            $table->text('keterangan')->nullable();

            // Kolom 'created_at' dan 'updated_at'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aturan_tenggat');
    }
};
