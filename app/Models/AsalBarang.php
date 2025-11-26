<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsalBarang extends Model
{
    protected $table = 'asal_barang';

    protected $fillable = [
        'id_barang',
        'id_area_pembelian',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function areaPembelian()
    {
        return $this->belongsTo(AreaPembelian::class, 'id_area_pembelian');
    }
}
