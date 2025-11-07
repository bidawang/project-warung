<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HutangBarangMasuk extends Model
{
    use HasFactory;

    // Menentukan nama tabel yang sesuai jika berbeda dari konvensi
    protected $table = 'hutang_barang_masuk';

    protected $fillable = [
        'id_warung',
        'id_barang_masuk',
        'total',
        'status',
    ];

    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }

    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class, 'id_barang_masuk');
    }
}
