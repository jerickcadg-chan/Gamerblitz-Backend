<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * AppLog can be used to log error from client side
 */
class AppLog extends Model
{
    protected $fillable = [
        'level',
        'message',
        'stack_trace',
        'url',
        'user_agent',
        'ip_address',
        'user_id',
        'source',
        'context',
        'payload',
    ];
}
