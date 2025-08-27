<?php

namespace App\Models;

use App\Constants\DefaultRole;
use App\Traits\WhereByClient;
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
    use WhereByClient;

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
        return $query->whereHas('roles', function ($query) {
            return $query->where('name', '!=', DefaultRole::CUSTOMER);
        });
    }

    public function scopeCustomer($query)
    {
        return $query->whereHas('roles', function ($query) {
            return $query->where('name', DefaultRole::CUSTOMER);
        });
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords(strtolower($value));
    }

    /**
     * Get highest level
     *
     */
    public function getRoleAttribute()
    {
        if ($this->hasRole(DefaultRole::RESELLER_VIP)) {
            return DefaultRole::RESELLER_VIP;
        }
        if ($this->hasRole(DefaultRole::RESELLER_GOLD)) {
            return DefaultRole::RESELLER_GOLD;
        }
        if ($this->hasRole(DefaultRole::RESELLER_SILVER)) {
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

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class)->withDefault([
            'id' => 0,
            'first_name' => $this->name,
            'last_name' => '',
            "user_id" => $this->id,
            "created_at" =>  '',
            "updated_at" => '',
            'whatsapp_number' => convert_to_62($this->phone_number),
        ]);
    }

    public function fullName(): Attribute
    {
        $profile = $this->profile;
        return Attribute::make(
            get: fn() => $profile->first_name . ' ' . $profile->last_name
        );
    }
}
