<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AturanTenggat extends Model
{
    use HasFactory;

    protected $table = 'aturan_tenggat';

    protected $fillable = [
        'id_warung',
        'tanggal_awal',
        'tanggal_akhir',
        'jatuh_tempo_hari',
        'bunga',
        'keterangan'
    ];

    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }
}
