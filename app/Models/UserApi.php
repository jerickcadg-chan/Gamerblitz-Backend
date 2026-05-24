<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserApi extends Model
{
    /**
     * @var string
     */
    protected $table = 'user_apis';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 
        'token', 
        'callback_url', 
        'callback_token'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
