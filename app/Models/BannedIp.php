<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannedIp extends Model
{
    protected $fillable = ['ip_address', 'banned_at', 'ban_reason'];

    protected $casts = [
        'banned_at' => 'datetime',
    ];
}
