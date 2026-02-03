<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('satuan', function (Blueprint $table) {
            $table->id();
            $table->string('kategori_satuan'); // Contoh: Dus, Pack, Box
            $table->string('nama_satuan');     // Contoh: Pack Isi 6, Pack Isi 10
            $table->integer('jumlah')->default(1);
            $table->timestamps();
        });

        // Seeding data awal dengan variasi isi
        DB::table('satuan')->insert([
            // Kategori PCS
            [
                'kategori_satuan' => 'Pcs',
                'nama_satuan' => 'Pcs',
                'jumlah' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Kategori PACK dengan variasi isi
            [
                'kategori_satuan' => 'Pack',
                'nama_satuan' => 'Pack Isi 6',
                'jumlah' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kategori_satuan' => 'Pack',
                'nama_satuan' => 'Pack Isi 10',
                'jumlah' => 10,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Kategori DUS dengan variasi isi
            [
                'kategori_satuan' => 'Dus',
                'nama_satuan' => 'Dus Kecil (Isi 24)',
                'jumlah' => 24,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kategori_satuan' => 'Dus',
                'nama_satuan' => 'Dus Besar (Isi 40)',
                'jumlah' => 40,
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Kategori Lainnya
            [
                'kategori_satuan' => 'Lusin',
                'nama_satuan' => 'Lusin',
                'jumlah' => 12,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satuan');
    }
};
