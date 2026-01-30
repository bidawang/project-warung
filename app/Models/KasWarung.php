<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasWarung extends Model
{
    use HasFactory;

    protected $table = 'kas_warung';

    protected $fillable = [
        'id_warung',
        'jenis_kas',
        'saldo',
        'keterangan'
    ];

    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }

    public function detailKasWarung()
    {
        return $this->hasMany(DetailKasWarung::class, 'id_kas_warung');
    }

    public function updateSaldo($jumlah, $operasi = 'tambah')
    {
        if ($operasi === 'tambah') {
            $this->saldo += $jumlah;
        } else {
            $this->saldo -= $jumlah;
        }
        return $this->save();
    }
}
