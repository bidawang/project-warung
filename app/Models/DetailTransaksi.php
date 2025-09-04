<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    use HasFactory;

    protected $table = 'detail_transaksi';

    protected $fillable = [
        'id_transaksi',
        'pecahan',
        'jumlah',
        'keterangan'
    ];

    public function transaksi()
    {
        return $this->belongsTo(TransaksiKas::class, 'id_transaksi');
    }
}