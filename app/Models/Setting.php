<?php

namespace App\Models;

use App\Constants\CurrencyConstant;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public const KEY_BASE_CURRENCY = 'base_currency';
    public const KEY_XENDIT_API_URL = 'xendit_api_url';
    public const KEY_XENDIT_SECRET_KEY = 'xendit_secret_key';
    public const KEY_XENDIT_CALLBACK_KEY = 'xendit_callback_key';

    protected $fillable = [
        'key', 'value'
    ];

    public static function getBaseCurrency(): string
    {
        return static::getByKey(self::KEY_BASE_CURRENCY) ?? CurrencyConstant::DEFAULT_BASE_CURRENCY;
    }

    public static function getByKey(string $key)
    {
        // TODO: cache
        return static::where('key', $key)->value('value');
    }
}
