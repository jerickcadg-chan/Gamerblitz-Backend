<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use IndexZer0\EloquentFiltering\Filter\FilterType;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function filter(): array
    {
        $filters = request()->filters;
        if (!$filters) {
            return [];
        }

        $filteredFilter = [];
        foreach ($filters as $filter) {
            if ($filter['target'] && $filter['type'] && $filter['value']) {
                if (str($filter['target'])->contains('date') && $filter['type'] == FilterType::EQUAL->value || str($filter['target'])->contains('created_at')) {
                    $filter['value'] = Carbon::parse($filter['value'])->format('Y-m-d h:i:s');
                }
                if ($filter['type'] == FilterType::BETWEEN->value) {
                    $filter['value'] = str($filter['value'])->explode(',')->toArray();
                }
                $filteredFilter[] = $filter;
            }
        }

        return $filteredFilter;
    }
}
