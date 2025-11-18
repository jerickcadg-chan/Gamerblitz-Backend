<?php

namespace App\Events;

use App\Models\Order;
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
     * @var Order
     */
    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('orders.updated'),
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
        $code = $this->order->code;
        $maskedCode = substr($code, 0, 7) . 'XXXXX';

        $price = (string) $this->order->total_price;
        $maskedAmount = substr($price, 0, 2) . 'XXXX';

        return [
            'code' => $maskedCode,
            'amount' => $maskedAmount,
            'product' => $this->order->productItem->full_name,
            'thumbnail' => $this->order->productItem->product->product_picture,
            'status' => $this->order->status,
        ];
    }
}
