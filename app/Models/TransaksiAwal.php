<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiAwal extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transaksi_awal';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'total',
        'keterangan',
    ];

    // app/Models/TransaksiAwal.php

    public function detailsLain()
    {
        // Parameter kedua adalah Foreign Key yang ada di tabel transaksi_lain_lain
        return $this->hasMany(TransaksiLainLain::class, 'id_transaksi_awal', 'id');
    }
}
