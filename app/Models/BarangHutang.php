<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangHutang extends Model
{
    use HasFactory;

    protected $table = 'barang_hutang';

    protected $fillable = [
        'id_hutang',
        'id_transaksi_barang',
    ];

    public function hutang(): BelongsTo
    {
        return $this->belongsTo(hutang::class, 'id_hutang', 'id');
    }
    public function transaksiBarang(): BelongsTo
    {
        return $this->belongsTo(TransaksiBarang::class, 'id_transaksi_barang', 'id');
    }
}
