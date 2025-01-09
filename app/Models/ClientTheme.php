<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperClientTheme
 */
class ClientTheme extends Model
{
    protected $fillable = [
        'mg_bg',
        'mg_fg',
        'mg_fg_alt',
        'mg_accent_1',
        'mg_accent_2',
        'mg_accent_3',
        'mg_accent_4',
        'mg_accent_5',
        'mg_accent_6',
        'mg_border',
        'mg_scrollbar',
        'fg_btn',
        'fg_btn_secondary',
        'fg_btn_outline',
        'mg_bg_1',
        'mg_bg_2',
        'mg_bg_3',
        'mg_bg_4',
        'mg_bg_5',
        'mg_bg_accent_1',
        'bg_btn',
        'bg_btn_secondary',
        'mg-accent-success',
        'mg-accent-error',
        'mg-accent-warning',
    ];
}
