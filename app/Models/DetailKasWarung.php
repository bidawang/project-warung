<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKasWarung extends Model
{
    use HasFactory;

    protected $table = 'detail_kas_warung';

    protected $fillable = [
        'id_kas_warung',
        'pecahan',
        'jumlah',
        'keterangan'
    ];

    public function kasWarung()
    {
        return $this->belongsTo(KasWarung::class, 'id_kas_warung');
    }
}