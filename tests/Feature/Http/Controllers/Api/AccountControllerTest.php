<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Account;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createClient()
    {
        return Client::factory()->create([
            'host' => 'localhost.test',
        ]);
    }

    private function setIndexUrl($filter = []): string
    {
        return str(route('product.account', $filter))->replace(config('app.url'), 'http://localhost.test');
    }

    public function test_product_account_index()
    {
        $client = $this->createClient();
        $account = Account::factory()->count(5)->create([
            'client_id' => $client->id,
        ])->each(function (Account $account) {
            $account->productItem()->update([
                'type' => 'account',
            ]);
            $account->picture()->create([
                'path' => 'account.jpg',
                'type' => 'cover',
                'file_name' => 'account.jpg',
            ]);
        });

        $response = $this->get($this->setIndexUrl());

        $responseData = collect($account)
            ->sortByDesc('created_at')
            ->map(function (Account $account) {
                return [
                    'id' => $account->id,
                    'slug' => (string) $account->slug,
                    'product_item_id' => $account->product_item_id,
                    'title' => $account->title,
                    'description' => $account->description,
                    'code' => $account->code,
                    'winrate' => $account->winrate,
                    'skin' => $account->skin,
                    'heroes' => $account->heroes,
                    'discount_type' => $account->discount_type,
                    'discount_amount' => (float) $account->discount_amount,
                    'discount' => $account->discount,
                    'real_price' => (float) $account->productItem->price,
                    'discount_price' => $account->price,
                    'cover_images' => $account->pictures->map(fn ($picture) => $picture->url)->toArray(),
                ];
            })->toArray();

        $response->assertStatus(200);

        $this->assertSame($responseData[0], $response->json('payload.data')[0]);
    }

    public function test_product_can_be_filtered_with_price_min()
    {
        $client = $this->createClient();
        $min = 200000;
        Account::factory()->count(3)->create([
            'client_id' => $client->id,
            'discount_type' => null,
            'discount_amount' => 0,
        ])->each(function (Account $account) use ($min) {
            $account->productItem()->update([
                'type' => 'account',
                'price' => $min,
            ]);
            $account->picture()->create([
                'path' => 'account.jpg',
                'type' => 'cover',
                'file_name' => 'account.jpg',
            ]);
        });

        Account::factory()->count(2)
            ->create([
                'client_id' => $client->id,
                'discount_type' => null,
                'discount_amount' => 0,
            ])->each(function (Account $account) {
                $account->productItem()->update([
                    'type' => 'account',
                    'price' => 100000,
                ]);
                $account->picture()->create([
                    'path' => 'account.jpg',
                    'type' => 'cover',
                    'file_name' => 'account.jpg',
                ]);
            });

        $filters = [
            "filters" => [
                [
                    'target' => 'price',
                    'type'   => '$gte',
                    'value'  => $min
                ]
            ]
        ];

        $response = $this->get($this->setIndexUrl($filters));

        $this->assertCount(3, $response->json('payload.data'));
    }

    public function test_product_can_be_filtered_with_price_max()
    {
        $client = $this->createClient();
        $max = 200000;
        Account::factory()->count(3)->create([
            'client_id' => $client->id,
            'discount_type' => null,
            'discount_amount' => 0,
        ])->each(function (Account $account) use ($max) {
            $account->productItem()->update([
                'type' => 'account',
                'price' => $max,
            ]);
            $account->picture()->create([
                'path' => 'account.jpg',
                'type' => 'cover',
                'file_name' => 'account.jpg',
            ]);
        });
        Account::factory()->count(2)
            ->create([
                'client_id' => $client->id,
                'discount_type' => null,
                'discount_amount' => 0,
            ])->each(function (Account $account) {
                $account->productItem()->update([
                    'type' => 'account',
                    'price' => 300000,
                ]);
                $account->picture()->create([
                    'path' => 'account.jpg',
                    'type' => 'cover',
                    'file_name' => 'account.jpg',
                ]);
            });
        $filters = [
            "filters" => [
                [
                    'target' => 'price',
                    'type'   => '$lte',
                    'value'  => $max
                ]
            ]
        ];
        $response = $this->get($this->setIndexUrl($filters));
        $this->assertCount(3, $response->json('payload.data'));
    }

    public function test_product_can_be_filtered_with_price_min_and_max()
    {
        $client = $this->createClient();
        $min = 150000;
        $max = 250000;

        Account::factory()->count(2)->create([
            'client_id' => $client->id,
            'discount_type' => null,
            'discount_amount' => 0,
        ])->each(function (Account $account) use ($min) {
            $account->productItem()->update([
                'type' => 'account',
                'price' => $min,
            ]);
            $account->picture()->create([
                'path' => 'account.jpg',
                'type' => 'cover',
                'file_name' => 'account.jpg',
            ]);
        });

        Account::factory()->count(3)->create([
            'client_id' => $client->id,
            'discount_type' => null,
            'discount_amount' => 0,
        ])->each(function (Account $account) use ($max) {
            $account->productItem()->update([
                'type' => 'account',
                'price' => $max,
            ]);
            $account->picture()->create([
                'path' => 'account.jpg',
                'type' => 'cover',
                'file_name' => 'account.jpg',
            ]);
        });

        Account::factory()->count(2)->create([
            'client_id' => $client->id,
            'discount_type' => null,
            'discount_amount' => 0,
        ])->each(function (Account $account) {
            $account->productItem()->update([
                'type' => 'account',
                'price' => 300000,
            ]);
            $account->picture()->create([
                'path' => 'account.jpg',
                'type' => 'cover',
                'file_name' => 'account.jpg',
            ]);
        });

        $filters = [
            "filters" => [
                [
                    'target' => 'price',
                    'type'   => '$gte',
                    'value'  => $min
                ],
                [
                    'target' => 'price',
                    'type'   => '$lte',
                    'value'  => $max
                ]
            ]
        ];

        $response = $this->get($this->setIndexUrl($filters));

        $this->assertCount(5, $response->json('payload.data'));
    }

    public function test_product_can_filter_by_skin(): void
    {
        $client = $this->createClient();
        $skin = 200;
        Account::factory()->count(3)->create([
            'client_id' => $client->id,
            'skin' => $skin,
        ])->each(function (Account $account) {
            $account->productItem()->update([
                'type' => 'account',
            ]);
        });
        Account::factory()->count(2)
            ->create([
                'client_id' => $client->id,
                'skin' => 100,
            ])->each(function (Account $account) {
                $account->productItem()->update([
                    'type' => 'account',
                ]);
            });
        $filters = [
            "filters" => [
                [
                    'target' => 'skin',
                    'type'   => '$eq',
                    'value'  => $skin
                ]
            ]
        ];
        $response = $this->get($this->setIndexUrl($filters));

        $this->assertCount(3, $response->json('payload.data'));
    }

    public function test_product_can_filtered_by_the_heroes(): void
    {
        $client = $this->createClient();
        $heroes = 200;
        Account::factory()->count(3)->create([
            'client_id' => $client->id,
            'heroes' => $heroes,
        ])->each(function (Account $account) {
            $account->productItem()->update([
                'type' => 'account',
            ]);
        });
        Account::factory()->count(2)
            ->create([
                'client_id' => $client->id,
                'heroes' => 100,
            ])->each(function (Account $account) {
                $account->productItem()->update([
                    'type' => 'account',
                ]);
            });
        $filters = [
            "filters" => [
                [
                    'target' => 'heroes',
                    'type'   => '$eq',
                    'value'  => $heroes
                ]
            ]
        ];
        $response = $this->get($this->setIndexUrl($filters));

        $this->assertCount(3, $response->json('payload.data'));
    }

    public function test_product_can_filterd_by_winrate(): void
    {
        $client = $this->createClient();
        $winrate = 200;
        Account::factory()->count(3)->create([
            'client_id' => $client->id,
            'winrate' => $winrate,
        ])->each(function (Account $account) {
            $account->productItem()->update([
                'type' => 'account',
            ]);
        });
        Account::factory()->count(2)
            ->create([
                'client_id' => $client->id,
                'winrate' => 100,
            ])->each(function (Account $account) {
                $account->productItem()->update([
                    'type' => 'account',
                ]);
            });
        $filters = [
            "filters" => [
                [
                    'target' => 'winrate',
                    'type'   => '$gte',
                    'value'  => $winrate
                ]
            ]
        ];

        $response = $this->get($this->setIndexUrl($filters));

        $this->assertCount(3, $response->json('payload.data'));
    }


    public function test_user_can_see_the_product(): void
    {
        $client = $this->createClient();
        $account = Account::factory()->count(3)->create([
            'client_id' => $client->id,
        ])->each(function (Account $account) {
            $account->productItem()->update([
                'type' => 'account',
            ]);
            $account->picture()->create([
                'path' => 'account.jpg',
                'type' => 'cover',
                'file_name' => 'account.jpg',
            ]);
        });
        $account = $account->first();
        $response = $this->get(str(route('product.account.show', $account->slug))->replace(config('app.url'), 'http://localhost.test'));

        $responseData = [
            'id' => $account->id,
            'slug' => (string) $account->slug,
            'product_item_id' => $account->product_item_id,
            'title' => $account->title,
            'description' => $account->description,
            'code' => $account->code,
            'winrate' => $account->winrate,
            'skin' => $account->skin,
            'heroes' => $account->heroes,
            'discount_type' => $account->discount_type,
            'discount_amount' => (float) $account->discount_amount,
            'discount' => $account->discount,
            'real_price' => (float) $account->productItem->price,
            'discount_price' => $account->price,
            'cover_images' => $account->pictures->map(fn ($picture) => $picture->url)->toArray(),
        ];
        $response->assertStatus(200);
        $this->assertSame($responseData, $response->json('payload'));
    }

    public function test_user_only_see_list_account_that_has_stock_more_than_0(): void
    {
        $client = $this->createClient();
        Account::truncate();
        Account::factory()->count(5)->create([
            'client_id' => $client->id,
        ])->each(function (Account $account) {
            if ($account->id === 1) {
                $account->productItem()->update([
                    'type' => 'account',
                    'stock' => 0,
                ]);
            } else {
                $account->productItem()->update([
                    'type' => 'account',
                    'stock' => 1,
                ]);
            }
            $account->picture()->create([
                'path' => 'account.jpg',
                'type' => 'cover',
                'file_name' => 'account.jpg',
            ]);
        });
        $response = $this->get($this->setIndexUrl());
        $this->assertCount(4, $response->json('payload.data'));
    }
}
