<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetPencapaian extends Model
{
    use HasFactory;

    protected $table = 'target_pencapaian';

    protected $fillable = [
        'id_warung',
        'periode_awal',
        'periode_akhir',
        'tercapai',
        'target_pencapaian',
        'status_pencapaian',
        'keterangan'
    ];

    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }
}
