<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('harga_jual', function (Blueprint $table) {
            // Menambahkan kategori dengan tipe enum
            $table->boolean('status')->default(false);
        });
    }
    public function down()
    {
        Schema::table('harga_jual', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
    }

};
