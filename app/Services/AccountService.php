<?php

namespace App\Services;

use App\Constants\ProductConstant;
use App\Http\Requests\AccountStoreRequest;
use App\Http\Requests\AccountUpdateRequest;
use App\Models\Account;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductItem;
use Exception;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function store(AccountStoreRequest $request): Account
    {
        DB::beginTransaction();

        try {
            $product = $this->createOrUpdateProduct();
            $productItem = $this->createProductItem($product, $request);
            $this->createProductItemClient($productItem);
            $productClient = $this->createProductClient($product);

            $account = $this->createAccount($request, $productItem, $productClient);

            DB::commit();

            return $account;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(AccountUpdateRequest $request, Account $account): Account
    {
        DB::beginTransaction();

        try {
            $productItem = $this->updateProductItem($account->productItem, $request);
            $account = $this->updateAccount($request, $account, $productItem);

            DB::commit();

            return $account;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(Account $account): void
    {
        DB::beginTransaction();

        try {
            $this->deleteProductItemClient($account->productItem);
            $this->deleteProductClient($account->productItem->product);
            $this->deleteProductItem($account->productItem);
            $account->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function deleteProductItemClient(ProductItem $productItem): void
    {
        $productItemClient = $productItem->productItemClients->firstWhere('client_id', client()->id);
        if ($productItemClient) {
            $productItemClient->delete();
        }
    }

    private function deleteProductClient(Product $product): void
    {
        $productClient = $product->productClient->firstWhere('client_id', client()->id);
        if ($productClient) {
            $productClient->delete();
        }
    }

    private function deleteProductItem(ProductItem $productItem): void
    {
        $productItem->delete();
    }

    private function createOrUpdateProduct(): Product
    {
        /** @var Product $product */
        $product = Product::whereCategory(ProductConstant::ACCOUNT)->firstOrCreate([
            'name' => 'Akun game',
            'code' => 'AKUN',
            'description' => 'Akun game',
            'company' => '-',
            'how_to_order' => '-',
            'category' => ProductConstant::ACCOUNT,
            'status' => Product::ACTIVE,
        ]);

        return $product;
    }

    private function updateProductItem(ProductItem $productItem, AccountUpdateRequest $request)
    {
        $productItem->update([
            'name' => $request->title,
            'code' => $request->code,
            'stock' => $request->stock,
            'price' => $request->price,
            'price_reseller' => 0,
            'capital' => 0,
        ]);

        return $productItem;
    }

    private function createProductItem(Product $product, AccountStoreRequest $request)
    {
        return $product->productItems()->create([
            'name' => $request->title,
            'code' => $request->code,
            'stock' => $request->stock,
            'price' => $request->price,
            'price_reseller' => 0,
            'capital' => 0,
        ]);
    }

    private function createProductItemClient($productItem)
    {
        $productItem->productItemClients()->create([
            'client_id' => client()->id,
            'margin' => 0,
            'is_active' => true,
        ]);
    }

    private function createProductClient(Product $product)
    {
        return $product->productClient()->create([
            'client_id' => client()->id,
            'is_active' => true,
        ]);
    }

    private function updateAccount(AccountUpdateRequest $request, Account $account, $productItem): Account
    {
        $account->fill($request->validated());
        $account->productItem()->associate($productItem);
        $account->save();

        return $account;
    }

    private function createAccount(AccountStoreRequest $request, $productItem, $productClient): Account
    {
        $account = new Account();
        $account = $account->fill($request->validated());
        $account->productItem()->associate($productItem);
        $account->client()->associate($productClient->client);
        $account->save();

        return $account;
    }
}
