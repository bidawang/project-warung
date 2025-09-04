<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laba extends Model
{
    use HasFactory;

    protected $table = 'laba';

    protected $fillable = [
        'id_area',
        'input_minimal',
        'input_maksimal',
        'harga_jual',
        'jenis',
        'keterangan'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area');
    }
}