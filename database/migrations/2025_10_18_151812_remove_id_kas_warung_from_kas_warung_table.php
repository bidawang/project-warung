use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi (hapus kolom id_kas_warung).
     */
    public function up(): void
    {
        Schema::table('kas_warung', function (Blueprint $table) {
            if (Schema::hasColumn('kas_warung', 'id_kas_warung')) {
                $table->dropColumn('id_kas_warung');
            }
        });
    }

    /**
     * Kembalikan migrasi (tambahkan kolom kembali jika di-rollback).
     */
    public function down(): void
    {
        Schema::table('kas_warung', function (Blueprint $table) {
            $table->bigIncrements('id_kas_warung')->first();
        });
    }
};
