<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'id_sub_kategori',
        'kode_barang',
        'nama_barang',
        'keterangan'
    ];

    public function subKategori()
    {
        return $this->belongsTo(SubKategori::class, 'id_sub_kategori');
    }

    public function transaksiBarang()
    {
        return $this->hasMany(TransaksiBarang::class, 'id_barang');
    }

    public function stokWarung()
    {
        return $this->hasMany(StokWarung::class, 'id_barang');
    }

    public function hargaJual()
    {
        return $this->hasMany(HargaJual::class, 'id_barang');
    }
}
