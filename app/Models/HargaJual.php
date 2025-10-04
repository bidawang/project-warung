<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // Import Attribute class

class HargaJual extends Model
{
    use HasFactory;

    protected $table = 'harga_jual';

    protected $fillable = [
        'id_warung',
        'id_barang',
        'harga_sebelum_markup',
        'harga_modal',
        'harga_jual_range_awal',
        'harga_jual_range_akhir',
        'periode_awal',
        'periode_akhir',
    ];

    // Relasi ke model Warung
    public function warung()
    {
        return $this->belongsTo(Warung::class, 'id_warung');
    }

    // Relasi ke model Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    /**
     * Accessor untuk menghitung dan memformat persentase laba (margin).
     * Format: "X%" atau "X% - Y%"
     *
     * Rumus: ((Harga Jual - Harga Modal) / Harga Modal) * 100
     */
    protected function persentaseLaba(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $modal = $attributes['harga_modal'];
                $jualAwal = $attributes['harga_jual_range_awal'];
                $jualAkhir = $attributes['harga_jual_range_akhir'];

                // Cek modal untuk menghindari pembagian dengan nol
                if ($modal === null || $modal <= 0) {
                    return 'N/A';
                }

                // Hitung persentase laba awal
                $persenAwal = (($jualAwal - $modal) / $modal) * 100;
                // Hitung persentase laba akhir
                $persenAkhir = (($jualAkhir - $modal) / $modal) * 100;

                // Format angka ke integer (membulatkan)
                $persenAwalFormatted = number_format($persenAwal, 0);
                $persenAkhirFormatted = number_format($persenAkhir, 0);

                // Tentukan format output
                if ($persenAwalFormatted === $persenAkhirFormatted) {
                    return "{$persenAwalFormatted}%";
                }

                return "{$persenAwalFormatted}% - {$persenAkhirFormatted}%";
            }
        );
    }
}
