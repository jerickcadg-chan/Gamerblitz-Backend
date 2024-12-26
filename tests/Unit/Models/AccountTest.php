<?php

namespace Tests\Unit\Models;

use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function testFactory()
    {
        $account = Account::factory()->create();

        foreach (array_keys($account->getAttributes()) as $fillable) {
            $this->assertNotNull($account->{$fillable});
        }
    }

    public function testFillable()
    {
        $account = new Account();
        $this->assertEquals([
            'title',
            'description',
            'code',
            'winrate',
            'skin',
            'heroes',
            'information',
        ], $account->getFillable());
    }

    public function testDates()
    {
        $account = new Account();
        $this->assertEquals(['created_at', 'updated_at'], $account->getDates());
    }

    public function testToArray()
    {
        $account = Account::factory()->create();
        $array = $account->toArray();

        $this->assertSame(array_keys($array), [
            "product_item_id",
            "client_id",
            "title",
            'information',
            "description",
            "slug",
            "code",
            "winrate",
            "skin",
            "heroes",
            "updated_at",
            "created_at",
            "id",
        ]);
    }

    public function test_belongs_to_product_item()
    {
        $account = Account::factory()->create();
        $this->assertNotEmpty($account->productItem);
    }

    public function test_belongs_to_client()
    {
        $account = Account::factory()->create();
        $this->assertNotEmpty($account->client);
    }

    public function test_account_should_be_has_slug_base_on_title_and_code()
    {
        $account = Account::factory()->create([
            'title' => 'Account Title',
            'code' => '1234',
        ]);
        $this->assertEquals('account-title-1234', $account->slug);
    }
}
