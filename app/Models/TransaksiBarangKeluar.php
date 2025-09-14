<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiBarangKeluar extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaksi_barang_keluar';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_transaksi_kas',
        'id_barang_keluar',
        'jumlah',
    ];

    /**
     * Get the transaksi_kas that owns the TransaksiBarangKeluar.
     */
    public function transaksiKas()
    {
        return $this->belongsTo(TransaksiKas::class, 'id_transaksi_kas');
    }

    /**
     * Get the barang_keluar that owns the TransaksiBarangKeluar.
     */
    public function barangKeluar()
    {
        return $this->belongsTo(BarangKeluar::class, 'id_barang_keluar');
    }
}