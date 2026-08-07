<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreamerHistory extends Model
{
    protected $fillable = [
        'streamer_id',
        'order_id',
        'order_amount',
        'commission_amount',
        'commission_rate',
        'status',
    ];

    protected $casts = [
        'order_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
    ];

    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
