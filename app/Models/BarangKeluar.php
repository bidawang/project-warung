<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'barang_keluar';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_stok_warung',
        'jumlah',
        'jenis',
        'keterangan',
    ];

    public function stokWarung()
    {
        return $this->belongsTo(StokWarung::class, 'id_stok_warung');
    }

    public function transaksiBarangKeluar()
    {
        return $this->hasOne(TransaksiBarangKeluar::class, 'id_barang_keluar');
    }

    public function barangHutang()
    {
        // return $this->hasMany(BarangHutang::class, 'id_barang_keluar');
        return $this->hasOne(BarangHutang::class, 'id_barang_keluar');
    }


}
