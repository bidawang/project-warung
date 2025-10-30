<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPulsa extends Model
{
    use HasFactory;
    protected $table = 'transaksi_pulsa';
    protected $fillable = ['id_pulsa', 'id_kas_warung', 'jumlah', 'jenis_pembayran', 'total', 'jenis', 'tipe'];


    public function hutang()
    {
        // Asumsi: TransaksiPulsa memiliki foreign key 'id_hutang'
        return $this->belongsTo(Hutang::class, 'id_hutang');
    }

    // Asumsi Anda juga memiliki relasi pulsa:
    public function pulsa()
    {
        // Asumsi: TransaksiPulsa memiliki foreign key 'id_pulsa'
        return $this->belongsTo(Pulsa::class, 'id_pulsa');
    }

    // Relasi: Satu TransaksiPulsa dimiliki oleh satu KasWarung
    public function kasWarung()
    {
        return $this->belongsTo(KasWarung::class, 'id_kas_warung');
    }
}
