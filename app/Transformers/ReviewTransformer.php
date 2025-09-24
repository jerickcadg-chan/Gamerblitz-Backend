<?php

namespace App\Transformers;

use App\Models\Review;
use League\Fractal\TransformerAbstract;

class ReviewTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param Review $review
     * @return array
     */
    public function transform(Review $review): array
    {
        return [
            'created_at' => $review->created_at,
            'star' => $review->star,
            'body' => $review->body,
        ];
    }
}
