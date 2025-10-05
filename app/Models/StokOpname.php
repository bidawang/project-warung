<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    use HasFactory;

    protected $table = 'stok_opname';

    protected $fillable = [
        'id_stok_warung',
        'jumlah',
    ];

    public function stokWarung()
    {
        return $this->belongsTo(StokWarung::class, 'id_stok_warung');
    }
}
