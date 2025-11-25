<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PageDescription extends Model
{
    /**
     * @var string
     */
    protected $table = 'page_descriptions';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug'
    ];

    /**
     * @return HasOne
     */
    public function content(): HasOne
    {
        return $this->hasOne(PageDescriptionContent::class);
    }
}
