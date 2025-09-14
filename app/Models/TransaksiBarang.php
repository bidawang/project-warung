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
        'id_area_pembelian',
        'jumlah',
        'harga',
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

    public function barangMasuk()
    {
        return $this->hasOne(BarangMasuk::class, 'id_transaksi_barang');
    }

    public function areaPembelian()
    {
        return $this->belongsTo(AreaPembelian::class, 'id_area_pembelian');
    }
}
