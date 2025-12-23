<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum pada kolom jenis
        DB::statement("
            ALTER TABLE transaksi_kas
            MODIFY jenis ENUM('penjualan','hutang','expayet','hilang') NOT NULL
        ");
    }

    public function down(): void
    {
        // Kembalikan enum ke bentuk semula
        DB::statement("
            ALTER TABLE transaksi_kas
            MODIFY jenis ENUM('penjualan barang','penjualan pulsa','hutang barang','hutang pulsa','expayet','hilang','masuk','keluar') NOT NULL
        ");
    }
};
