<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'name' => ($user->profile?->first_name && $user->profile?->last_name) ? $user->full_name : $user->name,
            'email' => $user->email,
            'phone_number' => $user?->phone_number,
            'address' => $user->address,
            'created_at' => parse_date_time($user->created_at),
            'role' => $user->role,
            'profile' => $user->profile,
        ];
    }
}
