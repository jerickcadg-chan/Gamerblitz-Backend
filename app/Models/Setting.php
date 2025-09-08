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

    public const KEY_LAPAKGAMING_API_URL = 'lapakgaming_api_url';
    public const KEY_LAPAKGAMING_API_TOKEN = 'lapakgaming_api_token';
    public const KEY_LAPAKGAMING_IP = 'lapakgaming_ip';

    protected static ?array $loaded = null;

    protected $fillable = [
        'key', 'value'
    ];

    public static function getBaseCurrency(): string
    {
        return static::getByKey(self::KEY_BASE_CURRENCY) ?? CurrencyConstant::DEFAULT_BASE_CURRENCY;
    }

    public static function getByKey(string $key)
    {
        if (static::$loaded === null) {
            static::$loaded = static::pluck('value', 'key')->all();
        }

        return static::$loaded[$key] ?? null;
    }
}
