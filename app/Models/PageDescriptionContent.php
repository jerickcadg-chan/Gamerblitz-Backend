<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageDescriptionContent extends Model
{
    /**
     * @var string
     */
    protected $table = 'page_description_contents';

    /**
     * @var array
     */
    protected $fillable = [
        'page_description_id',
        'type',
        'title',
        'content'
    ];

    /**
     * @return BelongsTo
     */
    public function pageDescription(): BelongsTo
    {
        return $this->belongsTo(PageDescription::class);
    }
}
