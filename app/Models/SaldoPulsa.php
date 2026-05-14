<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoPulsa extends Model
{
    protected $table = 'saldo_pulsa';
    protected $fillable = ['id_warung', 'id_jenis', 'jumlah'];

    public function jenisPulsa()
    {
        return $this->belongsTo(JenisPulsa::class, 'id_jenis');
    }

    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }
}