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
        'keterangan'
    ];

    protected $casts = [
        'status' => HutangStatusEnum::class,
    ];

    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}

enum HutangStatusEnum: string
{
    case LUNAS = 'lunas';
    case BELUM_LUNAS = 'belum lunas';
}