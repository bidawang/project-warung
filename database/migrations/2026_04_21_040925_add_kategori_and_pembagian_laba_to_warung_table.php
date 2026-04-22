<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('warung', function (Blueprint $table) {
            // Menambahkan kategori dengan tipe enum
            $table->enum('kategori', ['grosir', 'warung'])->default('warung')->after('modal');

            // Menambahkan pembagian laba dengan default 70|30 (Pengelola|Owner)
            $table->string('pembagian_laba')->default('70|30')->after('kategori');
        });
    }

    public function down()
    {
        Schema::table('warung', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'pembagian_laba']);
        });
    }
};
