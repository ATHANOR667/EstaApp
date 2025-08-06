<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContractContentGenerationFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $error;
    public $componentId;

    public function __construct(string $error, string $componentId)
    {
        $this->error = $error;
        $this->componentId = $componentId;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('contrat-form-modal.' . $this->componentId);
    }
}
