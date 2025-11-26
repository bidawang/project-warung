<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaPembelian extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'area_pembelian';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'area',
        'markup',
    ];

    public function barangs()
    {
        return $this->belongsToMany(
            Barang::class,
            'asal_barang',
            'id_area_pembelian',    // FK di tabel pivot
            'id_barang'             // FK tujuan
        );
    }
}
