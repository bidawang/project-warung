<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiBarang extends Model
{
    use HasFactory;

    protected $table = 'mutasi_barang';

    protected $fillable = [
        'id_stok_warung',
        'warung_asal',
        'warung_tujuan',
        'jumlah',
        'status',
        'keterangan'
    ];
    public function stokWarung()
    {
        return $this->belongsTo(StokWarung::class, 'id_stok_warung');
    }

    public function warungAsal()
    {
        return $this->belongsTo(Warung::class, 'warung_asal');
    }

    public function warungTujuan()
    {
        return $this->belongsTo(Warung::class, 'warung_tujuan');
    }
}