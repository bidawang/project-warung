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
        'id_pulsa'
    ];

    public function pulsa()
    {
        return $this->belongsTo(Pulsa::class, 'id_pulsa');
    }

    // Di dalam file App\Models\JenisPulsa.php
    public function saldoPulsa()
    {
        return $this->hasOne(SaldoPulsa::class, 'id_jenis');
    }
}
