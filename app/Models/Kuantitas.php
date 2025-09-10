<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kuantitas extends Model
{
    use HasFactory;

    protected $table = 'kuantitas';

    protected $fillable = [
        'id_stok_warung',
        'jumlah',
        'harga_jual',
    ];

    public function stokWarung(): BelongsTo
    {
        return $this->belongsTo(StokWarung::class, 'id_stok_warung', 'id');
    }
}