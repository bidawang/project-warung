<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UangPelanggan extends Model
{
    use HasFactory;

    protected $table = 'uang_pelanggan';

    protected $fillable = [
        'transaksi_id',
        'uang_dibayar',
        'uang_kembalian',
    ];

    public function transaksi()
    {
        return $this->belongsTo(TransaksiAwal::class);
    }
}
