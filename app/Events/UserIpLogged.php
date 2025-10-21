<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserIpLogged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $ipAddress;
    public $action;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $ipAddress, $action)
    {
        $this->userId = $userId;
        $this->ipAddress = $ipAddress;
        $this->action = $action;
    }


}
