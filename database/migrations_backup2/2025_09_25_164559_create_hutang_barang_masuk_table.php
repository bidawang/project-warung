<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHutangBarangMasukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hutang_barang_masuk', function (Blueprint $table) {
            $table->id(); // Kolom 'id' (Primary Key, Auto-Increment)

            $table->foreignId('id_warung')->constrained('warung')->onDelete('cascade');

            $table->foreignId('id_barang_masuk')->constrained('barang_masuk')->onDelete('cascade');

            $table->integer('total'); // Kolom 'total'

            $table->timestamps(); // Kolom 'created_at' dan 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hutang_barang_masuk');
    }
}
