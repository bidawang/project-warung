<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaPulsa extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     * Secara default, Laravel menggunakan bentuk jamak (harga_pulsas),
     * tetapi jika tabel Anda adalah 'harga_pulsa', definisikan secara eksplisit.
     *
     * @var string
     */
    protected $table = 'harga_pulsa';

    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'jumlah_pulsa',
        'harga',
    ];

    /**
     * Kolom-kolom yang harus dikonversi ke tipe data tertentu (Casting).
     * Karena 'jumlah_pulsa' dan 'harga' adalah DECIMAL, kita bisa mengkonversinya
     * menjadi float atau string.
     *
     * @var array
     */
    protected $casts = [
        'jumlah_pulsa' => 'float',
        'harga'        => 'float',
    ];

    // Jika Anda ingin mendefinisikan relasi (misalnya ke Model Pulsa)
    // public function pulsas()
    // {
    //     return $this->hasMany(Pulsa::class, 'id_harga_pulsa');
    // }
}
