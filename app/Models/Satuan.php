<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $table = 'satuan';

    protected $fillable = [
        'nama_satuan',
        'kategori_satuan',
        'jumlah',
    ];

    public function barang()
    {
        return $this->belongsToMany(
            Barang::class,
            'satuan_barang',
            'id_satuan',
            'id_barang'
        );
    }
}
