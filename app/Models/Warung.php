<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warung extends Model
{
    use HasFactory;

    protected $table = 'warung';

    protected $fillable = [
        'id_user',
        'id_area',
        'nama_warung',
        'modal',
        'keterangan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area');
    }

    public function stokWarung()
    {
        return $this->hasMany(StokWarung::class, 'id_warung');
    }
}