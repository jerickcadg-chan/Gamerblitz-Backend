<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EcommerceOrderStatusHistory extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'note',
        'user_id',
    ];

    public function order()
    {
        return $this->belongsTo(EcommerceOrder::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
