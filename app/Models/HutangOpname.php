<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HutangOpname extends Model
{
    use HasFactory;

    protected $table = 'hutang_opname';

    protected $fillable = [
        'id_hutang_barang_masuk',
        'id_stok_opname',
    ];

    public function hutangBarangMasuk()
    {
        return $this->belongsTo(HutangBarangMasuk::class, 'id_hutang_barang_masuk');
    }

    public function stokOpname()
    {
        return $this->belongsTo(StokOpname::class, 'id_stok_opname');
    }
}
