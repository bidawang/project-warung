<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPulsa extends Model
{
    use HasFactory;

    protected $table = 'jenis_pulsa';

    protected $fillable = [
        'nama_jenis',
    ];

    /**
     * Relasi ke harga pulsa
     * 1 jenis pulsa punya banyak harga pulsa
     */
    public function hargaPulsa()
    {
        return $this->hasMany(HargaPulsa::class, 'jenis_pulsa_id');
    }

    public function pulsa()
    {
        return $this->hasMany(Pulsa::class, 'jenis_pulsa_id');
    }
}
