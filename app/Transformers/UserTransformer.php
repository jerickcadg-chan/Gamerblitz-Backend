<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function __construct(public float $exchangeRate = 1)
    {
    }
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        $profile = $user?->profile;

        return [
            'id' => $user->id,
            'name' => ($profile?->first_name && $profile?->last_name) ? $user->full_name : $user->name,
            'email' => $user->email,
            'phone_number' => $profile?->whatsapp_number ?? $user?->phone_number,
            'address' => $user->address,
            'created_at' => parse_date_time($user->created_at),
            'role' => $user->role,
            'affiliate' => [
                'code' => $user->affiliate?->code ?? "",
                'status' => $user->affiliate?->status ?? "inactive",
                'balance' => ($user->affiliate?->balance ?? 0) * $this->exchangeRate,
            ],
        ];
    }
}
