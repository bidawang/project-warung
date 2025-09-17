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
        'id_barang_keluar',
    ];

    public function hutang(): BelongsTo
    {
        return $this->belongsTo(hutang::class, 'id_hutang', 'id');
    }
    public function barangKeluar(): BelongsTo
    {
        return $this->belongsTo(barangKeluar::class, 'id_barang_keluar', 'id');
    }
}
