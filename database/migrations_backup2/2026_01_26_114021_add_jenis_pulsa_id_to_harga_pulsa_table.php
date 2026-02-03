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
        Schema::table('harga_pulsa', function (Blueprint $table) {
            $table->foreignId('jenis_pulsa_id')
                ->nullable()
                ->after('id')
                ->constrained('jenis_pulsa')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('harga_pulsa', function (Blueprint $table) {
            $table->dropForeign(['jenis_pulsa_id']);
            $table->dropColumn('jenis_pulsa_id');
        });
    }
};
