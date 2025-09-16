<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pulsa extends Model
{
    use HasFactory;
    protected $table = 'pulsa';
    protected $fillable = ['id_warung', 'id_harga_pulsa', 'saldo', 'jenis'];

    // Relasi: Satu Pulsa dimiliki oleh satu HargaPulsa
    public function hargaPulsa()
    {
        return $this->belongsTo(HargaPulsa::class, 'id_harga_pulsa');
    }

    // Relasi: Satu Pulsa dimiliki oleh satu Warung
    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }

    // Relasi: Satu Pulsa bisa memiliki banyak TransaksiPulsa
    public function transaksiPulsa()
    {
        return $this->hasMany(TransaksiPulsa::class, 'id_pulsa');
    }
}
