<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pulsa', function (Blueprint $table) {

            // HAPUS kolom enum lama
            if (Schema::hasColumn('pulsa', 'jenis')) {
                $table->dropColumn('jenis');
            }

            // TAMBAH foreign key jenis_pulsa
            if (!Schema::hasColumn('pulsa', 'jenis_pulsa_id')) {
                $table->foreignId('jenis_pulsa_id')
                    ->after('id_warung')
                    ->constrained('jenis_pulsa')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pulsa', function (Blueprint $table) {

            // DROP FK
            if (Schema::hasColumn('pulsa', 'jenis_pulsa_id')) {
                $table->dropForeign(['jenis_pulsa_id']);
                $table->dropColumn('jenis_pulsa_id');
            }

            // BALIKIN enum lama
            if (!Schema::hasColumn('pulsa', 'jenis')) {
                $table->enum('jenis', ['hp', 'listrik'])->after('saldo');
            }
        });
    }
};
