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
        Schema::table('rencana_belanja', function (Blueprint $table) {
            // Menambahkan kolom keterangan setelah status, boleh kosong (nullable)
            $table->text('keterangan')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('rencana_belanja', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
