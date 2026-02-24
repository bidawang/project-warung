<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranPokokWarung extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_pokok_warung';

    protected $fillable = [
        'id_warung',
        'redaksi',
        'jumlah',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'jumlah' => 'decimal:2',
    ];

    // Relasi ke Warung
    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }
}
