<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreamerWithdraw extends Model
{
    protected $fillable = [
        'streamer_id',
        'amount',
        'payment_method',
        'account_name',
        'account_number',
        'status',
        'notes',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}