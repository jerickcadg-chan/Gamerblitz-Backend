<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'xendit_callback_token',
        'xendit_token',
        'host',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function productItemClients(): HasMany
    {
        return $this->hasMany(ProductItemClient::class);
    }

    public function productClients(): HasMany
    {
        return $this->hasMany(ProductClient::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class)
            ->where('client_id', $this->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Super Admin');
            });
    }

    public function clientTheme(): HasOne
    {
        $attributes = (new ClientTheme())->getFillable();
        $defaultColumn = [];
        foreach ($attributes as $attribute) {
            $defaultColumn[$attribute] = null;
        }

        return $this->hasOne(ClientTheme::class)->withDefault($defaultColumn);
    }

    public function clientAbout(): HasOne
    {
        $attributes = (new ClientAbout())->getFillable();
        $defaultColumn = [];
        foreach ($attributes as $attribute) {
            $defaultColumn[$attribute] = null;
            if ($attribute === 'app_logo_url') {
                $defaultColumn[$attribute] = $this->logo;
            }
            if ($attribute === 'app_name') {
                $defaultColumn[$attribute] = $this->name;
            }
            if ($attribute === 'app_description') {
                $defaultColumn[$attribute] = $this->description;
            }
        }

        return $this->hasOne(ClientAbout::class)->withDefault($defaultColumn);
    }
}
