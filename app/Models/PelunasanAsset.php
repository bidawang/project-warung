<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PelunasanAsset extends Model
{
    protected $table = 'pelunasan_asset';
    protected $fillable = [
        'id_asset',
        'jumlah_bayar'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'id_asset');
    }
}
