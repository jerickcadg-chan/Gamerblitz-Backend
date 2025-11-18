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
        'first_login',
        'google2fa_secret',
        'banned_at',
        'ban_reason'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'banned_at' => 'datetime'
    ];

    public function balance(): HasOne
    {
        return $this->hasOne(Balance::class);
    }

    public function payoutAccount(): HasOne
    {
        return $this->hasOne(PayoutAccount::class);
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
            $query->whereIn('name', [DefaultRole::CUSTOMER, DefaultRole::RESELLER_SILVER, DefaultRole::RESELLER_VIP, DefaultRole::RESELLER_GOLD]);
        });
    }

    public function scopeBanned($query)
    {
        return $query->whereNotNull('banned_at');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('banned_at');
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

    public function getResellerLevelAttribute(): string
    {
        $resellerRoles = $this->roles()->whereIn('name', [DefaultRole::RESELLER_SILVER, DefaultRole::RESELLER_GOLD, DefaultRole::RESELLER_VIP])->pluck('name');

        if ($resellerRoles->contains(DefaultRole::RESELLER_VIP)) {
            return DefaultRole::RESELLER_VIP;
        }
        if ($resellerRoles->contains(DefaultRole::RESELLER_GOLD)) {
            return DefaultRole::RESELLER_GOLD;
        }
        if ($resellerRoles->contains(DefaultRole::RESELLER_SILVER)) {
            return DefaultRole::RESELLER_SILVER;
        }
        return DefaultRole::CUSTOMER;
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
