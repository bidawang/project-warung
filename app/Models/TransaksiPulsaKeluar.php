<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPulsaKeluar extends Model
{
    use HasFactory;
    protected $table = 'transaksi_pulsa_keluar';
    protected $fillable =
    [
        'id_pulsa',
        'id_transaksi_kas',
        'jenis_pembayaran',
        'jumlah_pulsa',
        'total',
        'laba_pulsa',
        'laba_owner',
        'laba_adjustment',
        'laba_warung'
    ];

    public function saldoPulsa()
    {
        return $this->belongsTo(SaldoPulsa::class, 'id_pulsa');
    }

    public function hargaPulsa()
    {
        // Asumsi: TransaksiPulsa memiliki foreign key 'id_hutang'
        return $this->belongsTo(HargaPulsa::class, 'id_harga_pulsa');
    }

    // Asumsi Anda juga memiliki relasi pulsa:
    public function pulsa()
    {
        // Asumsi: TransaksiPulsa memiliki foreign key 'id_pulsa'
        return $this->belongsTo(Pulsa::class, 'id_pulsa');
    }

    public function transaksiKas()
    {
        return $this->belongsTo(TransaksiKas::class, 'id_transaksi_kas');
    }

    // Relasi: Satu TransaksiPulsa dimiliki oleh satu KasWarung
    public function transaksiKasWarung()
    {
        return $this->belongsTo(KasWarung::class, 'id_transaksi_kas');
    }
}
