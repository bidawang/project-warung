<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatuanBarang extends Model
{
    protected $table = 'satuan_barang';

    protected $fillable = [
        'id_barang',
        'id_satuan',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }
}
