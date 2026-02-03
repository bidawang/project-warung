<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('uang_pelanggan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaksi_id')
                ->constrained('transaksi_kas', 'id')
                ->cascadeOnDelete();

            $table->decimal('uang_dibayar', 15, 2);
            $table->decimal('uang_kembalian', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uang_pelanggan');
    }
};
