<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserActivityLogged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $ipAddress;
    public $action;

    /**
     * Create a new event instance.
     */
    public function __construct(?int $userId, string $ipAddress, string $action)
    {
        $this->userId = $userId;
        $this->ipAddress = $ipAddress;
        $this->action = $action;
    }


}
