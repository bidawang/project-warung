<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangMasuk extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     * Secara default, Laravel akan menganggap nama tabelnya 'barang_masuks'.
     * Jika nama tabel berbeda, Anda perlu menentukannya secara manual.
     * @var string
     */
    protected $table = 'barang_masuk';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     * @var array
     */
    protected $fillable = [
        'id_transaksi_barang',
        'id_stok_warung',
        'jumlah',
        'status',
    ];

    public function transaksiBarang(): BelongsTo
    {
        return $this->belongsTo(TransaksiBarang::class, 'id_transaksi_barang');
    }

    public function stokWarung(): BelongsTo
    {
        return $this->belongsTo(StokWarung::class, 'id_stok_warung');
    }
}