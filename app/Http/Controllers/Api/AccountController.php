<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ClientTheme;
use App\Transformers\SalesAccountTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

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

    public function appearance(Request $request)
    {
        $data = $request->except('client');
        $fillable = (new ClientTheme())->getFillable();
        $errorsField = [];
        foreach ($data as $key => $value) {
            if (!in_array($key, $fillable)) {
                $errorsField[] = $key;
            }
        }

        if (!empty($errorsField)) {
            return response()->json(['error' => 'Invalid fields', 'fields' => $errorsField], 422);
        }

        $client = client();
        $client->clientTheme()->updateOrCreate($data);
        $client->save();
        $gitlab = config('services.gitlab');
        $host = app()->environment('local') ? 'sample-client-1.pages.dev' : $client->host;
        $response = Http::withQueryParameters([
            'variables[INCLUDE_HOSTS]' => $host,
            'variables[CF_ENV]' => 'main',
            'ref' => 'main',
            'token' => $gitlab['token'],
            'variables[ENV]' => 'dev'
        ])->post("{$gitlab['url']}/api/v4/projects/{$gitlab['project_id']}/trigger/pipeline");

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to trigger pipeline'], 500);
        }

        return api_status_ok([], 'Client appearance updated');
    }
}
