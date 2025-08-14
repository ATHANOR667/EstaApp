<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContractContentGenerated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $cacheKey;
    public int $prestationId;
    public int $userId;

    public function __construct(string $cacheKey, int $prestationId, int $userId)
    {
        $this->cacheKey = $cacheKey;
        $this->prestationId = $prestationId;
        $this->userId = $userId;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('contrat-form-modal.' . $this->userId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'cacheKey' => $this->cacheKey,
            'prestationId' => $this->prestationId,
        ];
    }
}
