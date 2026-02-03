<?php
// database/migrations/xxxx_xx_xx_xxxxxx_remove_tanggal_from_log_pembayaran_hutang.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('log_pembayaran_hutang', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
    }

    public function down(): void
    {
        Schema::table('log_pembayaran_hutang', function (Blueprint $table) {
            $table->dateTime('tanggal')->after('jumlah_pembayaran');
        });
    }
};
