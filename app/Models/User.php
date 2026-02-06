<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'role',
        'name',
        'nomor_hp',
        'email',
        'keterangan'
    ];

    public function hutang()
    {
        return $this->hasMany(Hutang::class, 'id_user', 'id');
    }

    public function hutangs()
    {
        // Sesuaikan 'id_user' dengan nama foreign key di tabel hutang Anda
        return $this->hasMany(Hutang::class, 'id_user');
    }
}
