<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('aturan_tenggat', function (Blueprint $table) {
            // Mengubah tipe bunga menjadi decimal agar bisa menampung nominal Rupiah yang besar
            // 15 digit total, 2 digit di belakang koma (Rp 9.999.999.999.999,00)
            $table->decimal('bunga', 15, 2)->change();
            
            // Menambahkan kolom tipe_bunga untuk pilihan 'persen' atau 'nominal'
            // Default 'nominal' agar angka 5000 dihitung Rp 5.000, bukan 5000%
            $table->enum('tipe_bunga', ['persen', 'nominal'])->default('nominal')->after('bunga');
        });

        Schema::table('hutang', function (Blueprint $table) {
            // Menambahkan kolom total_bunga di tabel hutang untuk mencatat akumulasi bunga yang diberikan admin
            if (!Schema::hasColumn('hutang', 'total_bunga')) {
                $table->decimal('total_bunga', 15, 2)->default(0)->after('jumlah_sisa_hutang');
            }
        });
    }

    public function down()
    {
        Schema::table('aturan_tenggat', function (Blueprint $table) {
            $table->string('bunga', 50)->change();
            $table->dropColumn('tipe_bunga');
        });

        Schema::table('hutang', function (Blueprint $table) {
            $table->dropColumn('total_bunga');
        });
    }
};