<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPulsaMasuk extends Model
{
    protected $table = 'transaksi_pulsa_masuk';

    protected $fillable = [
        'id_pulsa',
        'id_hutang_warung',
        'jumlah',
        'harga_alomogada',
        'total',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function pulsa()
    {
        return $this->belongsTo(Pulsa::class, 'id_pulsa');
    }

    public function hutangWarung()
    {
        return $this->belongsTo(HutangWarung::class, 'id_hutang_warung');
    }
}
