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

    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
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
