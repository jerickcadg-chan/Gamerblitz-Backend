<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public string $code;

    /**
     * @var string
     */
    public string $status;

    /**
     * Create a new event instance.
     */
    public function __construct($code, $status)
    {
        $this->code = $code;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('orders.' . $this->code),
        ];
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'order.status.updated';
    }

    /**
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'code' => $this->code,
            'status' => $this->status,
        ];
    }
}
