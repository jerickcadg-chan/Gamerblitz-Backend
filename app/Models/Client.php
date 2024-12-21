<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperClient
 */
class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    public $fillable = [
        'name',
        'logo',
        'description',
        'user_token',
        'host',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function productItemClients()
    {
        return $this->hasMany(ProductItemClient::class);
    }
}
