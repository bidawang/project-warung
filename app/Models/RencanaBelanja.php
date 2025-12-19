<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaBelanja extends Model
{
    use HasFactory;

    /**
     * Nama tabel database.
     *
     * @var string
     */
    protected $table = 'rencana_belanja';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'id_warung',
        'id_barang',
        'jumlah_awal',
        'jumlah_dibeli',
        'jumlah_diterima',
        'status', //batal, pending, selesai
    ];

    /**
     * Mendefinisikan relasi ke model Warung.
     */
    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }

    /**
     * Mendefinisikan relasi ke model Barang.
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

}
