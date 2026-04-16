<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiBarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'transaksi_barang_masuk';

    protected $fillable = [
        'id_transaksi_awal',
        'id_barang',
        'id_area_pembelian',
        'jumlah',
        'jumlah_terpakai',
        'harga',
        'status',
        'jenis',
        'tanggal_kadaluarsa',
        'box'
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
    // App\Models\TransaksiBarangMasuk.php

    public function detailTransaksiBarangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_transaksi_barang_masuk');
    }
}
