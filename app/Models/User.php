<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'client_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function balance(): HasOne
    {
        return $this->hasOne(Balance::class);
    }

    public function scopeNonCustomer($query)
    {
        return $query->whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'Customer');
        });
    }

    public function scopeCustomer($query)
    {
        return $query->whereHas('roles', function ($query) {
            return $query->where('name', 'Customer');
        });
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords(strtolower($value));
    }

    public function getRoleAttribute()
    {
        $role = $this->roles;

        return count($role) > 1
            ? ($this->hasRole('Super Admin') ? $role->where('name', '!=', 'Super Admin')->first()->name : $role->first()->name)
            : ($role->first()->name ?? null);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function fullName(): Attribute
    {
        $profile = $this->profile;
        return Attribute::make(
            get: fn() => $profile->first_name . ' ' . $profile->last_name
        );
    }
}
