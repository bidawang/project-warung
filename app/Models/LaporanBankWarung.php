<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanBankWarung extends Model
{
    use HasFactory;

    protected $table = 'laporan_bank_warung';

    protected $fillable = [
        'id_kas_warung',
        'jumlah',
        'tipe',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function kasWarung()
    {
        return $this->belongsTo(KasWarung::class, 'id_kas_warung');
    }
}
