<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hutang extends Model
{
    use HasFactory;

    protected $table = 'hutang';

    protected $fillable = [
        'id_warung',
        'id_user',
        'jumlah',
        'tenggat',
        'status',
        'keterangan',
        'jumlah_hutang_awal',
        'jumlah_sisa_hutang',
    ];

    protected $casts = [
        'tenggat' => 'date', // FIX: Casting string tanggal menjadi objek Carbon
    ];

    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }

    public function logs()
    {
        // Sesuaikan 'id_hutang' dengan foreign key di tabel log kamu
        return $this->hasMany(LogPembayaranHutang::class, 'id_hutang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function bunga()
    {
        return $this->hasMany(Bunga::class, 'id_hutang');
    }
    public function barangHutang()
    {
        return $this->hasMany(BarangHutang::class, 'id_hutang');
    }
    public function transaksiKas()
    {
        return $this->hasMany(TransaksiKas::class, 'id_hutang');
    }
}
