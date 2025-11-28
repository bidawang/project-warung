<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanaUtama extends Model
{
    protected $table = 'dana_utama';

    protected $fillable = [
        'jenis_dana',
        'saldo',
    ];
}
