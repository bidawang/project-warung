<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiLainLain extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaksi_lain_lain';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_transaksi_awal',
        'keterangan',
        'harga',
    ];

    // app/Models/TransaksiLainLain.php

    public function transaksiAwal()
    {
        // Kita hubungkan id_transaksi_awal (FK) ke id di tabel transaksi_awal
        return $this->belongsTo(TransaksiAwal::class, 'id_transaksi_awal', 'id');
    }
}