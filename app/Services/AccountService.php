<?php

namespace App\Services;

use App\Constants\ProductConstant;
use App\Http\Requests\AccountStoreRequest;
use App\Http\Requests\AccountUpdateRequest;
use App\Models\Account;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductItem;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public function store(AccountStoreRequest $request): ?Account
    {
        DB::beginTransaction();

        try {
            $product = $this->createOrUpdateProduct();
            if (!$product) {
                return null;
            }
            $productItem = $this->createProductItem($product, $request);
            $this->createProductItemClient($productItem);
            $productClient = $this->createProductClient($product);

            $account = $this->createAccount($request, $productItem, $productClient);

            insert_pictures($request->cover_picture, $account);

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
            if ($request->hasFile('cover_picture')) {
                delete_pictures($account->pictures);
                insert_pictures($request->cover_picture, $account);
            }

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

    private function createOrUpdateProduct(): ?Product
    {
        $category = ProductCategory::whereSlug(ProductConstant::ACCOUNT)->first();

        if (!$category) {
            toast('Category not found', 'error');
            return null;
        }

        /** @var Product $product */
        $product = Product::query()
            ->whereHas('productCategory', function (Builder $query) {
                $query->where('slug', ProductConstant::ACCOUNT);
            })->firstOrCreate([
                'name' => 'Akun game',
                'code' => 'AKUN',
                'description' => 'Akun game',
                'company' => '-',
                'how_to_order' => '-',
                'status' => Product::ACTIVE,
            ]);

        return $product;
    }

    private function updateProductItem(ProductItem $productItem, AccountUpdateRequest $request)
    {
        $productItem->update([
            'name' => $request->title,
            'code' => $request->code,
            'stock' => 1,
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
            'stock' => 1,
            'price' => $request->price,
            'price_reseller' => 0,
            'type' => ProductConstant::ACCOUNT,
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
        $data = $request->validated();
        if ($request->information != decrypt($account->information)) {
            $data = array_merge($request->validated(), [
                'information' => encrypt($request->information),
            ]);
        }
        if ($request->has('discount') && $request->get('discount')) {
            array_merge($data, [
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount,
            ]);
        }
        $account->fill($data);
        $account->productItem()->associate($productItem);
        $account->save();

        return $account;
    }

    private function createAccount(AccountStoreRequest $request, $productItem, $productClient): Account
    {
        $data = $request->validated();
        $account = new Account();
        if ($request->has('discount') && $request->get('discount')) {
            array_merge($data, [
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount,
            ]);
        }
        $account = $account->fill(array_merge($data, [
            'information' => encrypt($request->information),
        ]));
        $account->productItem()->associate($productItem);
        $account->client()->associate($productClient->client);
        $account->save();

        return $account;
    }
}
