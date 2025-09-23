<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'area';

    protected $fillable = [
        'area',
        'keterangan'
    ];

    public function aturanTenggat()
    {
        return $this->hasMany(AturanTenggat::class, 'id_area');
    }

    public function laba()
    {
        return $this->hasMany(Laba::class, 'id_area');
    }

    public function warung()
    {
        return $this->hasMany(Warung::class, 'id_area');
    }

    
}
