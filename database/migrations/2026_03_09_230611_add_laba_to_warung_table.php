<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warung', function (Blueprint $table) {
            $table->decimal('laba', 15, 2)->default(0)->after('nama_warung');
        });
    }

    public function down(): void
    {
        Schema::table('warung', function (Blueprint $table) {
            $table->dropColumn('laba');
        });
    }
};
