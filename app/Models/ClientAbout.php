<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperClientAbout
 */
class ClientAbout extends Model
{
    protected $fillable = [
        'app_name',
        'app_logo_url',
        'app_description',
        'contact_email',
        'contact_whatsapp',
        'contact_telegram',
        'social_youtube',
        'social_facebook',
        'social_instagram',
        'social_tiktok',
    ];
}
