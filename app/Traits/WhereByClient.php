<?php

namespace App\Traits;

trait WhereByClient
{
    public function scopeWhereByClient($query)
    {
        return $query->where('client_id', client()->id);
    }

    public function scopeWhereClient($query)
    {
        return $query->where('client_id', client()->id);
    }
}
