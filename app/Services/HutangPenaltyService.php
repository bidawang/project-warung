<?php

namespace App\Services;

use App\Models\Hutang;
use App\Models\AturanTenggat;
use Carbon\Carbon;

class HutangPenaltyService
{
    public function proses(): int
    {
        $count = 0;

        $hutangs = Hutang::where('status', '!=', 'lunas')
            ->whereDate('tenggat', '<', now())
            ->get();


        foreach ($hutangs as $hutang) {

            // if ($hutang->updated_at?->isToday()) {
            //     continue;
            // }

            $hariTelat = Carbon::parse($hutang->tenggat)
                ->diffInDays(now());

            $aturan = AturanTenggat::where('id_warung', $hutang->id_warung)
                ->where('tanggal_awal', '<=', $hariTelat)
                ->where('tanggal_akhir', '>=', $hariTelat)
                ->first();


            if (!$aturan) continue;

            $bunga = $hutang->jumlah_sisa_hutang * ($aturan->bunga / 100);


            $hutang->update([
                'jumlah_sisa_hutang' => $hutang->jumlah_sisa_hutang + $bunga,
                'tenggat' => Carbon::parse($hutang->tenggat)
                    ->addDays($aturan->jatuh_tempo_hari),
                'status' => 'lewat_tenggat',
            ]);

            $count++;
        }

        return $bunga;
    }
}
