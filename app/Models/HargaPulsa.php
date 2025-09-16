<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaPulsa extends Model
{
    use HasFactory;
    protected $table = 'harga_pulsa';
    protected $fillable = ['jumlah_pulsa', 'harga'];

    // Relasi: Satu HargaPulsa bisa dimiliki oleh banyak Pulsa
    public function pulsa()
    {
        return $this->hasMany(Pulsa::class, 'id_harga_pulsa');
    }
}