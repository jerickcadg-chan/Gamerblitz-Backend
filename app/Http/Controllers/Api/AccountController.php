<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Transformers\SalesAccountTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class AccountController extends Controller
{
    public function index()
    {
        $filter = $this->filter();
        $priceFilter = Arr::where($this->filter(), function ($value) {
            return $value["target"] === 'price';
        });
        if (count($priceFilter) > 0) {
            $productItem = [
                'type'   => '$has',
                'target' => 'productItem',
                'value' => $priceFilter
            ];
            $filter = Arr::except($filter, array_keys($priceFilter));
            $filter = [array_merge($filter, $productItem)];
        }
        $accounts = Account::whereByClient()
            ->filter($filter)
            ->latest()
            ->with('productItem.product', 'pictures')
            ->whereHas('productItem', function (Builder $query) {
                $query->where('type', 'account')
                    ->where('stock', '>', 0);
            });

        return api_status_ok(paginateTransformer(
            query: $accounts,
            transformer: new SalesAccountTransformer()
        ));
    }

    public function show(Account $account)
    {
        return api_status_ok(transformer(
            query: $account,
            transformer: new SalesAccountTransformer()
        ));
    }
}
