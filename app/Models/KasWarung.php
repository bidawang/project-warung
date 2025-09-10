<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasWarung extends Model
{
    use HasFactory;

    protected $table = 'kas_warung';

    protected $fillable = [
        'id_warung',
        'jenis_kas',
        'keterangan'
    ];

    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }
}
