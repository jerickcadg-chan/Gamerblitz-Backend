<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\FlashSaleController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SliderController;
use App\Models\FlashSale;
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

Route::get('slider', [SliderController::class, 'index']);

Route::get('check-nickname', [OrderController::class, 'checkNickname']);

Route::get('category', CategoryController::class);
Route::get('product', [ProductController::class, 'index']);
Route::get('product/paginate', [ProductController::class, 'paginate']);
Route::get('product/{product}', [ProductController::class, 'showProduct']);
Route::get('product-items/{productId}', [ProductController::class, 'getProductItems']);
Route::get('product-item/{id}', [ProductController::class, 'showProductItem']);
Route::get('flash-sale', FlashSaleController::class);

Route::get('payment-method', [OrderController::class, 'getPaymentMethods']);
Route::post('discount', [OrderController::class, 'getDiscount']);

Route::post('order', [OrderController::class, 'store']);
Route::get('order/{order}', [OrderController::class, 'show']);
Route::post('order/xendit', [OrderController::class, 'xenditCallback'])->name('callback.xendit');
Route::post('order/bangjeff', [OrderController::class, 'bangJeffCallback'])->name('callback.bangjeff');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::get('balance', [AuthController::class, 'myBalance']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('product-items/{productId}/auth', [ProductController::class, 'getProductItems']);

    Route::get('deposit/{code}', [DepositController::class, 'show']);
    Route::get('deposit', [DepositController::class, 'index']);
    Route::post('deposit', [DepositController::class, 'store']);

    Route::get('mutation', [DepositController::class, 'mutation']);

    Route::post('order/auth', [OrderController::class, 'store']);
    Route::get('order', [OrderController::class, 'index']);
    Route::get('order/{order}/auth', [OrderController::class, 'show']);
});
