<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContractContentGenerated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $content;
    public $componentId;

    public function __construct(string $content, string $componentId)
    {
        $this->content = $content;
        $this->componentId = $componentId;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('contrat-form-modal.' . $this->componentId);
    }
}
