<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HutangWarung extends Model
{
    use HasFactory;

    // Nama tabel didefinisikan secara eksplisit jika tidak mengikuti aturan plural Laravel
    protected $table = 'hutang_warung';

    /**
     * Kolom yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_warung','total', 'jenis', 'status']; // Tambah status

    /**
     * Casting tipe data kolom.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function hutangBarangMasuk()
    {
        return $this->hasMany(HutangBarangMasuk::class, 'id_hutang_warung');
    }
}
