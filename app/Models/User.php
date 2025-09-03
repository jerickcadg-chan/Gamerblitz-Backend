<?php

namespace App\Models;

use App\Constants\DefaultRole;
use App\Mail\SentVerificationLink;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use URL;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
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
        'first_login'
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
        return $query->whereHas('roles', function ($q) {
            $q->whereNotIn('name', [DefaultRole::CUSTOMER, DefaultRole::RESELLER_SILVER, DefaultRole::RESELLER_VIP, DefaultRole::RESELLER_GOLD]);
        });
    }

    public function scopeCustomer($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('name', DefaultRole::CUSTOMER);
        });
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['name'] = ucwords(strtolower($value));
    }

    /**
     * Get the highest level
     *
     */
    public function getRoleAttribute(): string
    {
        return $this->roles()->orderBy('id', 'desc')->first()->name;
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function affiliate(): HasOne
    {
        return $this->hasOne(Affiliate::class);
    }

    public function fullName(): Attribute
    {
        $profile = $this->profile;
        return Attribute::make(
            get: fn() => $profile->first_name . ' ' . $profile->last_name
        );
    }
}
