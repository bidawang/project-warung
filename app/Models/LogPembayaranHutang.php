<?php
// app/Models/LogPembayaranHutang.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogPembayaranHutang extends Model
{
    use HasFactory;

    protected $table = 'log_pembayaran_hutang';

    protected $fillable = [
        'id_hutang',
        'jumlah_pembayaran',
        'tanggal',
    ];

    /**
     * Relasi ke hutang
     */
    public function hutang()
    {
        return $this->belongsTo(Hutang::class, 'id_hutang');
    }
}
