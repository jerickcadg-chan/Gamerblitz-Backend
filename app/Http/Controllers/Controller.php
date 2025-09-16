<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use IndexZer0\EloquentFiltering\Filter\FilterType;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public function filter(): array
    {
        $filters = request()->input('filters', []);
        if (!$filters) {
            return [];
        }

        $filteredFilter = [];
        foreach ($filters as $filter) {
            if (!isset($filter['target'], $filter['type'], $filter['value'])) {
                continue;
            }

            if ((Str::contains($filter['target'], 'date') || Str::contains($filter['target'], 'created_at'))
                && $filter['type'] == FilterType::EQUAL->value) {
                $filter['value'] = Carbon::parse($filter['value'])->format('Y-m-d H:i:s');
            }

            if ($filter['type'] == FilterType::BETWEEN->value) {
                $filter['value'] = Str::of($filter['value'])->explode(',')->toArray();
            }

            $filteredFilter[] = $filter;
        }

        return $filteredFilter;
    }
}
