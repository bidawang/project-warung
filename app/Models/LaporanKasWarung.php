<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanKasWarung extends Model
{
    use HasFactory;

    protected $table = 'laporan_kas_warung';

    protected $fillable = [
        'id_kas_warung',
        'pecahan',
        'jumlah',
    ];

    public function kasWarung()
    {
        return $this->belongsTo(KasWarung::class, 'id_kas_warung');
    }
}
