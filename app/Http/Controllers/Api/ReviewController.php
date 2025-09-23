<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required,exists:orders,id',
            'star' => 'required,numeric|min:1|max:5',
            'review' => 'required'
        ]);

        $order = Order::with('productItem')->find($request->order_id);

        $review = Review::create([
            'order_id' => $order->id,
            'product_id' => $order->productItem->product_id,
            'star' => $request->star,
            'body' => $request->body,
        ]);

        return api_status_ok($review);
    }
}
