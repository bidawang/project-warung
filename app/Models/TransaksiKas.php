<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKas extends Model
{
    use HasFactory;

    protected $table = 'transaksi_kas';

    protected $fillable = [
        'id_kas_warung',
        'id_hutang',
        'total',
        'metode_pembayaran',
        'keterangan',
        'jenis',
    ];

    protected $casts = [
        'jenis' => JenisTransaksiEnum::class,
    ];

    public function kasWarung()
    {
        return $this->belongsTo(KasWarung::class, 'id_kas_warung');
    }
}

enum JenisTransaksiEnum: string
{
    case KELUAR = 'keluar';
    case MASUK = 'masuk';
    case PENDING = 'pending';
    case HUTANG = 'hutang';
}