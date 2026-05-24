<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FetchVarianJob extends Model
{
    /**
     * @var string
     */
    protected $table = 'fetch_varian_jobs';

    /**
     * @var array
     */
    protected $fillable = [
        'command_name',
        'status',
    ];
}
