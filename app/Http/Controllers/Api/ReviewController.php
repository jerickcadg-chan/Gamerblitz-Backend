<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Product $productId)
    {
        $query = Review::where('product_id', $productId);

        $total = $query->count();
        $average = round($query->avg('star'), 1);

        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $distribution[$i] = $query->where('star', $i)->count();
        }

        $reviews = Review::where('product_id', $productId)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($review) {
                return [
                    'user' => '*********',
                    'star' => $review->star,
                    'body' => $review->body,
                    'created_at' => $review->created_at,
                ];
            });

        return api_status_ok([
            'summary' => [
                'average_rating' => $average,
                'total_reviews' => $total,
                'distribution' => $distribution,
            ],
            'reviews' => $reviews,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'order_id' => 'required|exists:orders,id',
            'star' => 'required|min:1|max:5',
            'body' => 'required'
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
