<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BarangMasukUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $count;

    public function __construct($userId)
    {
        $this->userId = $userId;

        // Hitung jumlah notifikasi pending untuk user ini
        $this->count = \App\Models\BarangMasuk::whereHas('stokWarung.warung', function ($q) use ($userId) {
            $q->where('id_user', $userId);
        })->where('status', 'pending')->count();
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userId);
    }

    public function broadcastWith()
    {
        return ['count' => $this->count];
    }
}
