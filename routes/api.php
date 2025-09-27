<?php

use App\Http\Controllers\Api\AffiliateWithdrawController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\FlashSaleController;
use App\Http\Controllers\Api\ForgotPassword;
use App\Http\Controllers\Api\LapakGamingController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductItemCategoryController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\SliderController;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::fallback(function () {
    return api_status_warning('The route or data is Not Found!', 404);
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forgot-password', [ForgotPassword::class, 'forgot']);
Route::post('reset-password', [ForgotPassword::class, 'reset']);

Route::get('slider', [SliderController::class, 'index']);
Route::get('check-uid', [OrderController::class, 'checkUid']);
Route::get('category', CategoryController::class);
Route::get('product-item-category', [ProductItemCategoryController::class, 'index']);
Route::get('product-item-categories', [ProductItemCategoryController::class, 'indexWithMeta']);
Route::get('product', [ProductController::class, 'index']);
Route::get('product/paginate', [ProductController::class, 'paginate']);
Route::get('product/{product}', [ProductController::class, 'showProduct']);
Route::get('product-items/{productId}', [ProductController::class, 'getProductItems']);
Route::get('product-item/{id}', [ProductController::class, 'showProductItem']);
Route::get('flash-sale', FlashSaleController::class);

Route::get('payment-method', [OrderController::class, 'getPaymentMethods']);

Route::get('available-discounts', [DiscountController::class, 'availableDiscount']);
Route::post('check-discount', [DiscountController::class, 'checkDiscountCode']);

Route::post('order', [OrderController::class, 'store']);
Route::post('order/xendit', [OrderController::class, 'xenditCallback'])->name('callback.xendit');
Route::get('order/{order}', [OrderController::class, 'show']);

Route::get('setting', [SettingController::class, 'index']);
Route::get('currencies', [CurrencyController::class, 'index']);

Route::get('review/{product}', [ReviewController::class, 'index']);
Route::post('review', [ReviewController::class, 'store']);

Route::get('blogs', [BlogController::class, 'index']);
Route::get('blogs-all', [BlogController::class, 'all']);
Route::get('blog-categories', [BlogController::class, 'latestPerCategory']);
Route::get('blogs/{slug}', [BlogController::class, 'show']);

if (Schema::hasTable('settings')) {
    $lapakgamingIp = Setting::getByKey(Setting::KEY_LAPAKGAMING_IP);
    Route::middleware(['ip.whitelist:' . $lapakgamingIp])->group(function () {
        Route::post('callback/lapakgaming/product', [LapakGamingController::class, 'productUpdateCallback']);
        Route::post('callback/lapakgaming/order', [LapakGamingController::class, 'orderCallback']);
    });
}

Route::middleware(['auth:sanctum', 'only_verified'])->group(function () {
    Route::put('me', [AuthController::class, 'updateMe']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('balance', [AuthController::class, 'myBalance']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('product-items/{productId}/auth', [ProductController::class, 'getProductItems']);

    Route::get('deposit-metadata', [DepositController::class, 'metadata']);
    Route::get('deposit/{code}', [DepositController::class, 'show']);
    Route::get('deposit', [DepositController::class, 'index']);
    Route::post('deposit', [DepositController::class, 'store']);

    Route::get('mutation', [DepositController::class, 'mutation']);
    Route::get('affiliate-withdraw', [AffiliateWithdrawController::class, 'index']);
    Route::post('affiliate-withdraw/claim', [AffiliateWithdrawController::class, 'claim']);

    Route::get('order-stats', [OrderController::class, 'stats']);
    Route::post('order/auth', [OrderController::class, 'store']);
    Route::get('order', [OrderController::class, 'index']);
    Route::get('order/{order}/auth', [OrderController::class, 'show']);
});
