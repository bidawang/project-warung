<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HutangPenaltyService;

class ProsesHutangTenggat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    /**
     * The console command description.
     *
     * @var string
     */
    protected $signature = 'hutang:proses';
    protected $description = 'Proses bunga dan perpanjangan tenggat hutang';

    /**
     * Execute the console command.
     */
    public function handle(HutangPenaltyService $service)
    {
        $jumlah = $service->proses();

         $this->info('bunga: ' . $jumlah);

        // if ($jumlah === 0) {
        //     $this->info('✅ Tidak ada hutang yang lewat tenggat.');
        // } else {
        //     $this->warn("⚠️ {$jumlah} hutang lewat tenggat berhasil diproses.");
        // }
    }
}
