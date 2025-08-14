<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContractContentGenerationFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;
    public int $prestationId;
    public int $userId;

    public function __construct(array $data, int $prestationId, int $userId)
    {
        $this->data = $data;
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
            'error' => $this->data['error'],
            'prestationId' => $this->prestationId,
        ];
    }
}
