<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pulsa extends Model
{
    use HasFactory;
    protected $table = 'pulsa';
    protected $fillable = ['id_warung', 'id_jenis'];


    // Relasi: Satu Pulsa dimiliki oleh satu Warung
    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }

    public function jenisPulsa()
    {
        return $this->belongsTo(JenisPulsa::class, 'id_jenis');
    }

}
