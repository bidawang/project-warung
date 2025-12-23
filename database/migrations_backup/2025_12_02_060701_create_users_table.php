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
        // Pastikan tabel tidak ada sebelum membuat
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Kolom Utama
            $table->string('name'); // varchar(255), NO (NOT NULL)
            $table->string('role', 50); // varchar(50), NO (NOT NULL)

            // Kolom Identifikasi Unik
            $table->string('email')->unique(); // varchar(255), NO, UNI
            $table->string('google_id')->nullable()->unique(); // varchar(255), YES, UNI
            $table->string('nomor_hp', 20)->nullable()->unique(); // varchar(20), YES, UNI

            // Kolom Keamanan
            $table->timestamp('email_verified_at')->nullable(); // timestamp, YES
            $table->string('password'); // varchar(255), NO (NOT NULL)

            // Kolom Status dan Keterangan
            // enum('aktif','nonaktif'), NO, Default 'aktif'
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->text('keterangan')->nullable(); // text, YES

            // Kolom Laravel Bawaan
            $table->rememberToken(); // varchar(100), YES
            $table->timestamps(); // created_at dan updated_at (timestamp, YES)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
