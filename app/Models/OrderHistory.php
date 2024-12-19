<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperOrderHistory
 */
class OrderHistory extends Model
{
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
