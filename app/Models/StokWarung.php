<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokWarung extends Model
{
    use HasFactory;

    protected $table = 'stok_warung';

    protected $fillable = [
        'id_warung',
        'id_barang',
        'keterangan'
    ];

    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_stok_warung');
    }

    // Add a hasMany relationship to the MutasiBarang model
    public function mutasiBarang()
    {
        return $this->hasMany(MutasiBarang::class, 'id_stok_warung');
    }

    public function getStokAttribute()
    {
        $stokMasuk = $this->barangMasuk()
            ->where('status', 'terima')
            ->sum('jumlah');

        $mutasiMasuk = $this->mutasiBarang()
            ->where('status', 'terima')
            ->sum('jumlah');

        $mutasiKeluar = $this->mutasiBarang()
            ->where('status', 'keluar')
            ->sum('jumlah');

        return $stokMasuk + $mutasiMasuk - $mutasiKeluar;
    }
}
