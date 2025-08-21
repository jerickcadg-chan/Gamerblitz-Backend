<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\FlashSaleController;
use App\Http\Controllers\Api\ForgotPassword;
use App\Http\Controllers\Api\LapakGamingController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductItemCategoryController;
use App\Http\Controllers\Api\SliderController;
use App\Models\Client;
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

Route::get('check-nickname', [OrderController::class, 'checkNickname']);

Route::get('/clients/all', function () {
    $clients = Client::get();

    return api_status_ok(transformer($clients, \App\Transformers\ClientConfigurationTransformer::class));
})->middleware('basic_auth');

Route::get('category', CategoryController::class);
Route::get('product-item-category', [ProductItemCategoryController::class, 'index']);
Route::get('product-item-categories', [ProductItemCategoryController::class, 'indexWithMeta']);
Route::get('product/account', [AccountController::class, 'index'])->name('product.account');
Route::get('product/{account:slug}/account', [AccountController::class, 'show'])->name('product.account.show');
Route::get('product', [ProductController::class, 'index']);
Route::get('product/paginate', [ProductController::class, 'paginate']);
Route::get('product/{product}', [ProductController::class, 'showProduct']);
Route::get('product-items/{productId}', [ProductController::class, 'getProductItems']);
Route::get('product-item/{id}', [ProductController::class, 'showProductItem']);
Route::get('flash-sale', FlashSaleController::class);

Route::get('payment-method', [OrderController::class, 'getPaymentMethods']);
Route::post('discount', [OrderController::class, 'getDiscount']);

Route::post('order', [OrderController::class, 'store']);
Route::post('order/xendit', [OrderController::class, 'xenditCallback'])->name('callback.xendit');
Route::post('order/agen-callback', [OrderController::class, 'agenCallback'])->name('callback.bangjeff');
Route::get('order/{order}', [OrderController::class, 'show']);

Route::middleware(['ip.whitelist:' . config('array.lapakgaming.ip')])->group(function () {
    Route::post('callback/lapakgaming/product', [LapakGamingController::class, 'productUpdateCallback']);
});

Route::middleware(['auth:sanctum', 'only_verified'])->group(function () {
    Route::put('me', [AuthController::class, 'updateMe']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('balance', [AuthController::class, 'myBalance']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('product-items/{productId}/auth', [ProductController::class, 'getProductItems']);

    Route::get('deposit/{code}', [DepositController::class, 'show']);
    Route::get('deposit', [DepositController::class, 'index']);
    Route::post('deposit', [DepositController::class, 'store']);

    Route::get('mutation', [DepositController::class, 'mutation']);

    Route::get('order-stats', [OrderController::class, 'stats']);
    Route::post('order/auth', [OrderController::class, 'store']);
    Route::get('order', [OrderController::class, 'index']);
    Route::get('order/{order}/auth', [OrderController::class, 'show']);

    Route::post('/setting/fe/appearance', [AccountController::class, 'appearance']);
});
