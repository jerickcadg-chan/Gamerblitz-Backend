<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * @mixin IdeHelperVoucher
 */
class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_item_id', 'serial_number', 'password', 'capital', 'vendor', 'status', 'order_id'
    ];

    public function productItem()
    {
        return $this->belongsTo(ProductItem::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'ready':
                return '<label class="badge badge-success">Belum Dipakai</label>';
                break;

            case 'used':
                return '<label class="badge badge-success">Dipakai</label>';
                break;

            default:
                return '<label class="badge badge-warning">'. ucfirst($this->status) .'</label>';
                break;
        }
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function getPasswordDecryptedAttribute()
    {
        return Crypt::decryptString($this->password);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }
}
