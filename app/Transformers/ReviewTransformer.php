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
            'buyer' => $this->maskEmail($review->order->cust_email)
        ];
    }

    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $name = $parts[0] ?? '';
        $domain = $parts[1] ?? '';

        if (strlen($name) <= 2) {
            $maskedName = str_repeat('*', strlen($name));
        } else {
            $maskedName = substr($name, 0, 3) . str_repeat('*', max(strlen($name) - 3, 2));
        }

        return $maskedName . '@' . $domain;
    }
}
