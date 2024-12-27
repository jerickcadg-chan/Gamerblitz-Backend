<?php

namespace Tests\Feature\Http\Controllers;

use App\Constants\ProductConstant;
use App\Models\Account;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductClient;
use App\Models\ProductItem;
use App\Models\ProductItemClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_superadmin_can_access()
    {
        $user = $this->generateCustomerUser();

        $response = $this
            ->actingAs($user)
            ->get(route('account.index'));

        $response->assertSessionHas('alert');
        $this->assertToast($response, [
            'title' => trans('auth.no_permission'),
            'icon' => 'error',
        ]);

        $response->assertStatus(302);
    }

    public function test_index()
    {
        $user = $this->generateSuperAdminUser();
        $response = $this
            ->actingAs($user)
            ->get(route('account.index'));

        $response
            ->assertViewIs('accounts.index')
            ->assertStatus(200);
    }

    public function test_store_failed_cause_the_product_item_title_is_required()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => 10,
            ]);

        $response
            ->assertSessionHasErrors('title');
    }

    public function test_store_failed_cause_the_description_is_required()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => 10,
            ]);
        $response
            ->assertSessionHasErrors('description');
    }

    public function test_store_failed_cause_the_winrate_is_required()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'skin' => 10,
                'heroes' => 10,
            ]);
        $response
            ->assertSessionHasErrors('winrate');
    }

    public function test_store_failed_cause_the_winrate_must_be_numeric()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 'not-numeric',
                'skin' => 10,
                'heroes' => 10,
            ]);
        $response
            ->assertSessionHasErrors('winrate');
    }

    public function test_store_failed_cause_the_winrate_must_be_between_0_and_100()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 101,
                'skin' => 10,
                'heroes' => 10,
            ]);
        $response
            ->assertSessionHasErrors('winrate');
    }

    public function test_store_failed_cause_the_skin_is_required()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 10,
                'heroes' => 10,
            ]);
        $response
            ->assertSessionHasErrors('skin');
    }

    public function test_store_failed_cause_the_skin_must_be_numeric()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 'not-numeric',
                'heroes' => 10,
            ]);
        $response
            ->assertSessionHasErrors('skin');
    }

    public function test_store_failed_cause_the_skin_must_be_greater_than_or_equal_to_0()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => -1,
                'heroes' => 10,
            ]);
        $response
            ->assertSessionHasErrors('skin');
    }

    public function test_store_failed_cause_the_heroes_is_required()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
            ]);
        $response
            ->assertSessionHasErrors('heroes');
    }

    public function test_store_failed_cause_the_heroes_must_be_numeric()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => 'not-numeric',
            ]);
        $response
            ->assertSessionHasErrors('heroes');
    }

    public function test_store_failed_cause_the_heroes_must_be_greater_than_or_equal_to_0()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,
            ]);
        $response
            ->assertSessionHasErrors('heroes');
    }

    public function test_store_failed_cause_the_code_is_required()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,
            ]);
        $response
            ->assertSessionHasErrors('code');
    }

    public function test_store_failed_cause_the_code_must_be_unique()
    {
        Account::factory()->create(['code' => 'UNIQUE-12345']);
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'code' => 'UNIQUE-12345',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,
            ]);
        $response
            ->assertSessionHasErrors('code');
    }

    public function test_store_failed_cause_the_price_is_required()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'code' => 'ACC-12345',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,
            ]);
        $response
            ->assertSessionHasErrors('price');
    }

    public function test_store_failed_cause_the_price_must_be_numeric()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'code' => 'ACC-12345',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,

                'price' => 'not-numeric',
            ]);
        $response
            ->assertSessionHasErrors('price');
    }

    public function test_store_failed_because_information_is_required()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'code' => 'ACC-12345',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,

                'price' => 1000,
            ]);
        $response
            ->assertSessionHasErrors('information');
    }

    public function test_store_failed_when_discount_type_required()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'code' => 'ACC-12345',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,

                'price' => 1000,
                'information' => 'email=mail.test password=password',
                'discount' => true,
            ]);
        $response
            ->assertSessionHasErrors('discount_type');
    }

    public function test_store_failed_when_discount_amount_required()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'code' => 'ACC-12345',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,

                'price' => 1000,
                'information' => 'email=mail.test password=password',
                'discount' => true,
            ]);
        $response
            ->assertSessionHasErrors('discount_amount');
    }

    public function test_store_failed_when_discount_type_not_percentage_and_nominal()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'code' => 'ACC-12345',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,

                'price' => 1000,
                'information' => 'email=mail.test password=password',
                'discount' => true,
                'discount_type' => 'invalid',
            ]);
        $response
            ->assertSessionHasErrors('discount_type');
    }

    public function test_store_failed_when_discount_amount_max_100()
    {
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'code' => 'ACC-12345',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,

                'price' => 1000,
                'information' => 'email=mail.test password=password',
                'discount' => true,
                'discount_type' => 'percentage',
                'discount_amount' => 101,
            ]);
        $response
            ->assertSessionHasErrors('discount_amount');
    }

    public function test_store_success(): void
    {
        $admin = $this->generateSuperAdminUser();
        $mockImage = \Illuminate\Http\UploadedFile::fake()->image('mock-image.jpg');
        $data = [
            'title' => 'Title Test',
            'code' => 'ACC-12345',
            'description' => 'Description Test',
            'winrate' => 10,
            'skin' => 10,
            'heroes' => 10,
            'price' => 1000,
            'information' => 'email=mail.test password=password',
            'cover_picture' => $mockImage,
        ];
        $response = $this
            ->actingAs($admin)
            ->post(route('account.store'), $data);

        $response->assertSessionHasNoErrors();

        /** @var Account $account */
        $account = Account::whereSlug('title-test-acc-12345')->first();

        $this->assertNotNull($account, 'Account is not found');

        $this->assertSame($account->title, $data['title'], 'Title is not the same');
        $this->assertSame($account->code, $data['code'], 'Code is not the same');
        $this->assertSame($account->slug, str($data['title'])->slug()->append('-', str($data['code'])->slug())->value(), 'Slug is not the same');
        $this->assertSame($account->description, $data['description'], 'Description is not the same');
        $this->assertSame($account->winrate, (float) $data['winrate'], 'winrate is not the same');
        $this->assertSame($account->skin, $data['skin'], 'skin is not the same');
        $this->assertSame($account->heroes, $data['heroes'], 'heroes is not the same');
        $this->assertSame(decrypt($account->information), $data['information'], 'information is not the same');
        $this->assertNotNull($account->picture, 'Image is not found');

        $response->assertRedirect(route('account.show', $account));

        $this->assertToast($response, [
            'title' => alert_created_text('Akun'),
            'icon' => 'success',
        ]);

        $this->assertSame($account->client->toArray(), $admin->client->toArray(), 'Account client is not the same with the logged in user client');

        $productItem = $account->productItem;

        $this->assertSame($productItem->product->category, ProductConstant::ACCOUNT, 'Product category is not correct');
        $this->assertSame($productItem->type, 'account', 'Product item type is not account');
        $this->assertSame($productItem->name, $data['title'], 'Product item name is not correct');
        $this->assertSame($productItem->code, $data['code'], 'Product item code is not correct');
        $this->assertSame($productItem->price, $data['price'] . '.00', 'Product item price is not correct');

        $productClient = $productItem->product->productClient->firstWhere('client_id', $admin->client->id);

        $this->assertNotEmpty($productClient, 'Product client is not found');

        $productClientItem = $productItem->productItemClients->firstWhere('client_id', $admin->client->id);

        $this->assertNotEmpty($productClientItem, 'Product client item is not found');
    }

    public function test_store_with_discount(): void
    {
        $admin = $this->generateSuperAdminUser();
        $mockImage = \Illuminate\Http\UploadedFile::fake()->image('mock-image.jpg');
        $data = [
            'title' => 'Title Test',
            'code' => 'ACCUNIQUE-12345',
            'description' => 'Description Test',
            'winrate' => 10,
            'skin' => 10,
            'heroes' => 10,

            'price' => 1000,
            'information' => 'email=mail.test password=password',
            'cover_picture' => $mockImage,
            'discount' => true,
            'discount_type' => 'percentage',
            'discount_amount' => 20.0,
        ];
        $response = $this
            ->actingAs($admin)
            ->post(route('account.store'), $data);

        $response->assertSessionHasNoErrors();

        /** @var Account $account */
        $account = Account::whereSlug('title-test-accunique-12345')->first();
        $this->assertSame($account->discount_type, $data['discount_type'], 'Discount type is not the same');
        $this->assertSame((float)$account->discount_amount, $data['discount_amount'], 'Discount amount is not the same');
    }

    public function test_store_with_product_category_account_exist()
    {
        $admin = $this->generateSuperAdminUser();
        $client2 = Client::factory()->create([
            'host' => 'client2.test',
        ]);
        $admin2 = $this->generateSuperAdminUser();
        $admin2->client()->associate($client2);
        $admin2->save();

        $existingProduct = Product::whereCategory(ProductConstant::ACCOUNT)->firstOrCreate([
            'category' => ProductConstant::ACCOUNT,
            'name' => 'Akun game',
            'code' => 'AKUN',
            'description' => 'Akun game',
            'company' => '-',
            'how_to_order' => '-',
            'status' => Product::ACTIVE,
        ]);

        $this
            ->actingAs($admin)
            ->post(route('account.store'), [
                'title' => 'Title Test',
                'code' => 'ACC-12345',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => 10,

                'price' => 1000,
            ]);

        /** @var Account $account */
        $account = Account::whereSlug('title-test-acc-12345')->first();

        $this->assertSame($existingProduct->id, $account->productItem->product->id, 'Product is not the same with the existing product');
        $this->assertNotSame($account->client->toArray(), $admin2->client->toArray(), 'Account client is the same with the logged in user client');
    }

    public function test_update_success(): void
    {
        $admin = $this->generateSuperAdminUser();
        $account = Account::factory()->create([
            'title' => 'Old Title',
            'code' => 'UNIQUE2-12345',
            'description' => 'Old Description',
            'winrate' => 10,
            'skin' => 10,
            'heroes' => 10,
        ]);
        $picutre = $account->picture()
            ->create([
                'path' => 'old-path',
                'file_name' => 'old-filename',
                'url' => 'old-url',
            ]);

        $coverPicture = \Illuminate\Http\UploadedFile::fake()->image('new-image.jpg');

        $data = [
            'title' => 'New Title',
            'code' => $account->code,
            'description' => 'New Description',
            'winrate' => 20,
            'skin' => 20,
            'heroes' => 20,

            'price' => 2000,
            'cover_picture' => $coverPicture,
        ];

        $response = $this
            ->actingAs($admin)
            ->put(route('account.update', $account), $data);

        $response->assertSessionHasNoErrors();

        $account->refresh();

        $this->assertSame($data['title'], $account->title, 'Title is not the same');
        $this->assertSame($data['description'], $account->description, 'Description is not the same');
        $this->assertEquals((float) $data['winrate'], $account->winrate, 'Winrate is not the same');
        $this->assertSame($data['skin'], $account->skin, 'Skin is not the same');
        $this->assertSame($data['heroes'], $account->heroes, 'Heroes is not the same');
        $this->assertEquals((float) $data['price'], $account->productItem->price, 'Price is not the same');

        $this->assertNotSame($account->picture->url, $picutre->url, 'Picture url is the same');

        $response->assertRedirect(route('account.show', $account));

        $this->assertToast($response, [
            'title' => alert_updated_text('Akun'),
            'icon' => 'success',
        ]);
    }

    public function test_update_failed_cause_the_product_item_title_is_required()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => 10,
            ]);
        $response
            ->assertSessionHasErrors('title');
    }

    public function test_update_failed_cause_the_description_is_required()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => 10,
            ]);
        $response
            ->assertSessionHasErrors('description');
    }
    public function test_update_failed_cause_the_winrate_is_required()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'skin' => 10,
                'heroes' => 10,
            ]);
        $response
            ->assertSessionHasErrors('winrate');
    }

    public function test_update_failed_cause_the_winrate_must_be_numeric()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 'not-numeric',
                'skin' => 10,
                'heroes' => 10,
            ]);
        $response
            ->assertSessionHasErrors('winrate');
    }

    public function test_update_failed_cause_the_winrate_must_be_between_0_and_100()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 101,
                'skin' => 10,
                'heroes' => 10,
            ]);
        $response
            ->assertSessionHasErrors('winrate');
    }

    public function test_update_failed_cause_the_skin_is_required()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 10,
                'heroes' => 10,
            ]);
        $response
            ->assertSessionHasErrors('skin');
    }

    public function test_update_failed_cause_the_skin_must_be_numeric()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 'not-numeric',
                'heroes' => 10,
            ]);
        $response
            ->assertSessionHasErrors('skin');
    }

    public function test_update_failed_heroes_is_required()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
            ]);
        $response
            ->assertSessionHasErrors('heroes');
    }

    public function test_update_failed_cause_the_heroes_must_be_numeric()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => 'not-numeric',
            ]);
        $response
            ->assertSessionHasErrors('heroes');
    }

    public function test_update_failed_cause_the_heroes_must_be_greater_than_or_equal_to_0()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,
            ]);
        $response
            ->assertSessionHasErrors('heroes');
    }

    public function test_update_failed_cause_the_code_is_required()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,
            ]);
        $response
            ->assertSessionHasErrors('code');
    }

    public function test_update_failed_cause_the_code_must_be_unique()
    {
        Account::factory()->create(['code' => 'UNIQUE5-12345']);
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'code' => 'UNIQUE5-12345',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => 0,

                'price' => 1000,
            ]);
        $response
            ->assertSessionHasErrors('code');
    }

    public function test_update_failed_cause_the_price_is_required()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'code' => 'ACC-12345',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,

            ]);
        $response
            ->assertSessionHasErrors('price');
    }

    public function test_update_failed_cause_the_price_must_be_numeric()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'code' => 'ACC-12345',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,

                'price' => 'not-numeric',
            ]);
        $response
            ->assertSessionHasErrors('price');
    }

    public function test_update_failed_cause_the_price_must_be_greater_than_or_equal_to_0()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'code' => 'ACC-12345',
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => -1,

                'price' => -1,
            ]);
        $response
            ->assertSessionHasErrors('price');
    }

    public function test_update_unique_must_be_ignored_on_the_same_account()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->put(route('account.update', $account), [
                'title' => 'Title Test',
                'code' => $account->code,
                'description' => 'Description Test',
                'winrate' => 10,
                'skin' => 10,
                'heroes' => 10,

                'price' => 1000,
            ]);
        $response->assertSessionDoesntHaveErrors('code');
    }

    public function test_destroy_success(): void
    {
        $admin = $this->generateSuperAdminUser();
        $client = $admin->client;

        $account = Account::factory()->create();
        $productItem = $account->productItem;
        $response = $this
            ->actingAs($admin)
            ->delete(route('account.destroy', $account));
        $response->assertRedirect(route('account.index'));
        $this->assertToast($response, [
            'title' => alert_deleted_text('Akun'),
            'icon' => 'success',
        ]);
        $this->assertNull(ProductItemClient::whereClientId($client->id)->whereProductItemId($productItem->id)->first(), 'Product item client is not deleted');
        $this->assertNull(ProductClient::whereClientId($client->id)->whereProductId($productItem->product->id)->first(), 'Product client is not deleted');
        $this->assertNull(ProductItem::whereId($productItem->id)->first(), 'Product item is not deleted');
        $this->assertNull(Account::find($account->id), 'Account is not deleted');
    }

    public function test_show_information_failed_cause_the_pin_is_required()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.show-information', $account));

        $response->assertSessionHasErrors('pin');
    }

    public function test_show_information_failed_pin_should_be_6_length()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.show-information', $account), [
                'pin' => '12345',
            ]);

        $response->assertSessionHasErrors('pin');
    }

    public function test_show_information_failed_pin_should_be_string()
    {
        $account = Account::factory()->create();
        $response = $this
            ->actingAs($this->generateSuperAdminUser())
            ->post(route('account.show-information', $account), [
                'pin' => 123456.0,
            ]);

        $response->assertSessionHasErrors('pin');
    }

    public function test_show_the_information_success(): void
    {
        $admin = $this->generateSuperAdminUser();
        $account = Account::factory()->create();
        $pin = '123456';

        Http::fake([
            '*/api/v2/verify-pin' => Http::response(['status' => 'ok'], 200),
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('account.show-information', $account), [
                'pin' => $pin,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => decrypt($account->information),
        ]);
    }

    public function test_show_the_information_failed(): void
    {
        $admin = $this->generateSuperAdminUser();
        $account = Account::factory()->create();
        $pin = '123456';

        Http::fake([
            '*/api/v2/verify-pin' => Http::response(['status' => 'error'], 403),
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('account.show-information', $account), [
                'pin' => $pin,
            ]);

        $response->assertStatus(403);
    }
}
