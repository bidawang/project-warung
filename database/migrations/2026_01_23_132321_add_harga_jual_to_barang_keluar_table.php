<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->decimal('harga_jual', 15, 2)
                ->nullable()
                ->default(0)
                ->after('jumlah');
        });
    }

    public function down(): void
    {
        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropColumn('harga_jual');
        });
    }
};
