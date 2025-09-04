<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AturanTenggat extends Model
{
    use HasFactory;

    protected $table = 'aturan_tenggat';

    protected $fillable = [
        'id_area',
        'tanggal_awal',
        'tanggal_akhir',
        'jatuh_tempo_hari',
        'jatuh_tempo_bulan',
        'keterangan'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area');
    }
}