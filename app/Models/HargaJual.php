<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaJual extends Model
{
    use HasFactory;

    protected $table = 'harga_jual';

    protected $fillable = [
        'id_warung',
        'id_barang',
        'harga_modal',
        'harga_jual_range_awal',
        'harga_jual_range_akhir',
        'periode_awal',
        'periode_akhir',
    ];

    // Relasi ke model Warung
    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }

    // Relasi ke model Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
