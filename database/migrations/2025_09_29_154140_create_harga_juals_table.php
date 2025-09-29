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
        Schema::create('harga_jual', function (Blueprint $table) {
            $table->id(); // id (Primary Key)

            // Foreign Key ke tabel 'warung'
            $table->foreignId('id_warung')
                  ->constrained('warung') // Asumsi nama tabel adalah 'warung'
                  ->onDelete('cascade');

            // Foreign Key ke tabel 'barang'
            $table->foreignId('id_barang')
                  ->constrained('barang') // Asumsi nama tabel adalah 'barang'
                  ->onDelete('cascade');

            // Harga beli barang dari supplier
            $table->decimal('harga_modal', 15, 2);

            // Batas bawah harga jual yang diizinkan
            $table->decimal('harga_jual_range_awal', 15, 2);

            // Batas atas harga jual yang diizinkan
            $table->decimal('harga_jual_range_akhir', 15, 2);

            // Tanggal mulai periode penetapan harga ini
            $table->date('periode_awal');

            // Tanggal akhir periode penetapan harga ini
            $table->date('periode_akhir');

            // Opsional: Menjaga kombinasi ini unik
            // $table->unique(['id_warung', 'id_barang', 'periode_awal']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_jual');
    }
};
