<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'asset';
    protected $fillable = [
        'id_warung',
        'nama',
        'harga_asset',
        'tanggal_pembelian',
        'total_dibayar',
        'sisa_pembayaran',
        'keterangan'
    ];

    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }

    public function pelunasan()
    {
        return $this->hasMany(PelunasanAsset::class, 'id_asset');
    }
}