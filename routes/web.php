<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AffiliateWithdrawController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\FlashSaleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PictureController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductItemCategoryController;
use App\Http\Controllers\ProductItemController;
use App\Http\Controllers\ProductItemPriceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
use App\Models\BlogCategory;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/email/verify/{id}/mail/{mailhash}', function ($id, $mailhash) {
    $user = \App\Models\User::query()->findOrFail($id);
    if (!hash_equals(sha1($user->getEmailForVerification()), (string) $mailhash)) {
        return abort(401);
    }
    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();

        event(new Verified($user));
    }

    return redirect(config('app.fe_url') . '/login?verification=success');
})->middleware('signed')->name('verification');

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => true,
]);

Route::middleware(['web', 'auth', 'not_customer'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Voucher router
    Route::get('/voucher/import', [VoucherController::class, 'import'])->name('voucher.import');
    Route::post('/voucher/import-excel', [VoucherController::class, 'importExcel'])->name('voucher.import-excel');

    // Order router
    Route::get('order', [OrderController::class, 'index'])->name('order.index');
    Route::get('order/{order}', [OrderController::class, 'show'])->name('order.show');
    Route::post('order/status', [OrderController::class, 'setStatus'])->name('order.status');
    Route::post('order/process', [OrderController::class, 'triggerProcessOrder'])->name('order.process');

    // Deposit router
    Route::get('deposit', [DepositController::class, 'index'])->name('deposit.index');
    Route::put('deposit/{deposit}/status', [DepositController::class, 'updateStatus'])->name('deposit.update-status');
    Route::get('deposit/{deposit}', [DepositController::class, 'show'])->name('deposit.show');

    // User router
    Route::get('user/customer', [UserController::class, 'getCustomer'])->name('user.customer');
    Route::get('user/affiliate-withdraw', [AffiliateWithdrawController::class, 'index'])->name('user.affiliate-withdraw');
    Route::get('user/top-up-manual/{user}', [UserController::class, 'topUpManual'])->name('user.top-up-manual');
    Route::post('user/top-up-manual/{user}', [UserController::class, 'topUpManualStore'])->name('user.top-up-manual.store');

    // Report router
    Route::get('report', [ReportController::class, 'index'])->name('report.index');
    Route::post('report/order', [ReportController::class, 'getOrder'])->name('report.get.order');
    Route::post('report/user', [ReportController::class, 'getUser'])->name('report.get.user');

    // Statistic
    Route::get('statistic/order', [StatisticController::class, 'showOrderStatistic'])->name('statistic.order');
    Route::get('statistic/user', [StatisticController::class, 'showUserStatistic'])->name('statistic.user');

    // product item
    Route::get('product-item/price-form', [ProductItemPriceController::class, 'index'])->name('product-item.price-form');
    Route::put('product-item/price-form', [ProductItemPriceController::class, 'update'])->name('product-item.price-form-update');
    Route::post('product-item/sync-price', [ProductItemController::class, 'syncItem'])->name('product-item.sync');

    // product_item_category meta
    Route::group(['prefix' => 'product_item_category', 'as' => 'product_item_categories.'], function () {
        Route::get('/{product_item_category}/meta', [ProductItemCategoryController::class, 'metaCreate'])->name('metas.create');
        Route::get('/{product_item_category}/meta/{meta}', [ProductItemCategoryController::class, 'metaEdit'])->name('metas.edit');
        Route::post('/{product_item_category}/meta', [ProductItemCategoryController::class, 'metaStore'])->name('metas.store');
        Route::put('/{product_item_category}/meta/{meta}', [ProductItemCategoryController::class, 'metaUpdate'])->name('metas.update');
        Route::delete('/meta/{meta}', [ProductItemCategoryController::class, 'metaDestroy'])->name('metas.destroy');
    });

    Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
    Route::put('/setting', [SettingController::class, 'update'])->name('setting.update');

    Route::post('blog/upload-image', [BlogController::class, 'uploadImage'])->name('blog.upload_image');
    Route::post('picture/upload', [PictureController::class, 'store'])->name('picture.upload');

    Route::post('blog-categories/quick-store', function (\Illuminate\Http\Request $r) {
        $r->validate(['name'=>'required|string|max:150']);
        $slug = \Illuminate\Support\Str::slug($r->name);
        $cat = BlogCategory::firstOrCreate(['slug'=>$slug], ['name'=>$r->name]);
        return response()->json(['id'=>$cat->id, 'name'=>$cat->name]);
    })->name('blog-categories.quick-store');

    // Resource router
    Route::resources([
        'product_category' => ProductCategoryController::class,
        'product' => ProductController::class,
        'flash_sale' => FlashSaleController::class,
        'product_item' => ProductItemController::class,
        'product_item_category' => ProductItemCategoryController::class,
        'product_item_price' => ProductItemPriceController::class,
        'voucher' => VoucherController::class,
        'discount' => DiscountController::class,
        'slider' => SliderController::class,
        'user' => UserController::class,
        'role' => RoleController::class,
        'payment_method' => PaymentMethodController::class,
        'exchange_rate' => ExchangeRateController::class,
        'blog' => BlogController::class,
    ]);
});
