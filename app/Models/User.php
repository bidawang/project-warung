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
    public function kasir()
    {
        return $this->hasOne(Kasir::class, 'id_user');
    }
    public function member()
    {
        return $this->hasOne(Member::class, 'id_user');
    }
}
