<?php

namespace App\Serializers;

use League\Fractal\Serializer\DataArraySerializer;

class Serializer extends DataArraySerializer
{
    public function collection($resourceKey, array $data)
    {
        return $data;
    }

    public function item($resourceKey, array $data)
    {
        return $data;
    }

    public function null()
    {
        return null;
    }
}
