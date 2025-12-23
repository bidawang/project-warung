<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hutang', function (Blueprint $table) {
            // Ubah nama kolom jumlah_pokok jadi jumlah_hutang_awal
            $table->renameColumn('jumlah_pokok', 'jumlah_hutang_awal');

            // Tambahkan kolom jumlah_sisa_hutang
            $table->decimal('jumlah_sisa_hutang', 15, 2)->after('jumlah_hutang_awal');
        });
    }

    public function down(): void
    {
        Schema::table('hutang', function (Blueprint $table) {
            // Kembalikan perubahan jika rollback
            $table->renameColumn('jumlah_hutang_awal', 'jumlah_pokok');
            $table->dropColumn('jumlah_sisa_hutang');
        });
    }
};
