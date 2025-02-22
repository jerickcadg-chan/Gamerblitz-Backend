<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\FlashSaleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductItemCategoryController;
use App\Http\Controllers\ProductItemController;
use App\Http\Controllers\ProductItemPriceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
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

    return redirect(client()->frontend_host . '/login?verification=success');
})->middleware('signed')->name('verification');


Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => true,
]);


Route::get('/email/test', function () {
    return new \App\Mail\SentVerificationLink(
        user: \App\Models\User::query()->has('client')->first(),
        url: 'https://client-admin.test/email/verify/78/f99f902b40e9db817eb01e2bbb8bff6c697d4116?expires=1735435546&signature=1db807130bef166003562e0affa1bc29d88b8ed3aca144fa049b837583ed1659'
    );
});

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

    Route::resource('account', AccountController::class);
    Route::post('account/{account}/show-information', [AccountController::class, 'showTheInformation'])->name('account.show-information');
    // Resource router
    Route::resources([
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
    ]);
});
