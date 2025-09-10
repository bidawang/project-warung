<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiBarang extends Model
{
    use HasFactory;

    protected $table = 'transaksi_barang';

    protected $fillable = [
        'id_transaksi_kas',
        'id_barang',
        'jumlah',
        'status',
        'jenis',
        'keterangan'
    ];

    public function transaksiKas()
    {
        return $this->belongsTo(TransaksiKas::class, 'id_transaksi_kas');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}