<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutAccount extends Model
{
    /**
     * @var string
     */
    protected $table = 'payout_accounts';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'bank_name',
        'account_name',
        'account_number',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}