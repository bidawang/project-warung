<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update Tabel hutang_warung: Tambah kolom status
        Schema::table('hutang_warung', function (Blueprint $table) {
            $table->string('status')->default('belum lunas')->after('jenis');
        });

        // 2. Update Tabel barang_masuk: Tambah kolom total
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->decimal('total', 15, 2)->default(0)->after('jumlah');
        });

        // 3. Update Tabel hutang_barang_masuk: Tambah foreign key ke hutang_warung
        Schema::table('hutang_barang_masuk', function (Blueprint $table) {
            $table->foreignId('id_hutang_warung')
                ->nullable()
                ->after('id')
                ->constrained('hutang_warung')
                ->onDelete('cascade');
            
        });
    }

    public function down(): void
    {
        Schema::table('hutang_barang_masuk', function (Blueprint $table) {
            $table->dropForeign(['id_hutang_warung']);
            $table->dropColumn('id_hutang_warung');
            $table->dropColumn('total');

        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropColumn('total');
        });

        Schema::table('hutang_warung', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
