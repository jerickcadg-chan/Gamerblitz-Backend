<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceProductLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'action',
        'field_name',
        'old_value',
        'new_value',
        'description',
    ];

    public function product()
    {
        return $this->belongsTo(EcommerceProduct::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function logChange($productId, $action, $fieldName = null, $oldValue = null, $newValue = null, $description = null)
    {
        return self::create([
            'product_id' => $productId,
            'user_id' => auth()->id(),
            'action' => $action,
            'field_name' => $fieldName,
            'old_value' => is_array($oldValue) ? json_encode($oldValue) : $oldValue,
            'new_value' => is_array($newValue) ? json_encode($newValue) : $newValue,
            'description' => $description,
        ]);
    }
}