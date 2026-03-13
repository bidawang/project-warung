<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warung', function (Blueprint $attribute) {
            // Kita letakkan kolom hutang setelah kolom modal
            $attribute->decimal('hutang', 15, 2)->default(0)->after('modal');
        });
    }

    public function down(): void
    {
        Schema::table('warung', function (Blueprint $attribute) {
            $attribute->dropColumn('hutang');
        });
    }
};