<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\WithPictures;

class PaymentMethod extends Model
{
    use WithPictures;

    const QRIS = 'qris';
    const SALDO = 'saldo';

    protected $fillable = [
        'name', 'admin_fee', 'admin_type', 'slug', 'vendor'
    ];

    public function getDisplayNameAttribute()
    {
        return strtoupper(\str_replace('_', ' ', $this->name));
    }

    public function getAdminFeeTranslatedAttribute()
    {
        return $this->admin_type == 'percentage' ? (float)$this->admin_fee.'%' : rp_format($this->admin_fee);
    }
}
