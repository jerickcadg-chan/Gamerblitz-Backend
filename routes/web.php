<?php

use App\Http\Controllers\DepositController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
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

    // Deposit router
    Route::get('deposit', [DepositController::class, 'index'])->name('deposit.index');
    Route::put('deposit/{deposit}/status', [DepositController::class, 'updateStatus'])->name('deposit.update-status');
    Route::get('deposit/{deposit}', [DepositController::class, 'show'])->name('deposit.show');

    // User router
    Route::get('user/customer', [UserController::class, 'getCustomer'])->name('user.customer');

    // Report router
    Route::get('report', [ReportController::class, 'index'])->name('report.index');
    Route::post('report/order', [ReportController::class, 'getOrder'])->name('report.get.order');
    Route::post('report/user', [ReportController::class, 'getUser'])->name('report.get.user');

    // Resource router
    Route::resources([
        'product' => ProductController::class,
        'product_item' => ProductItemController::class,
        'voucher' => VoucherController::class,
        'discount' => DiscountController::class,
        'slider' => SliderController::class,
        'user' => UserController::class,
        'role' => RoleController::class,
    ]);
});
