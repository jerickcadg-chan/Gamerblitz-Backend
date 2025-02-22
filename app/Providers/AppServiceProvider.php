<?php

namespace App\Providers;

use App\Constants\ProductConstant;
use App\Constants\ProductJoki;
use App\Mail\SentVerificationLink;
use App\Models\FlashSaleProductItem;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Blade::directive('loadProductFlashSaleItem', function ($productFlashSaleItems) {
            $div = '';
            \App\Models\ProductItem::with('product')->chunk(200, function ($product_item) use (&$div) {
                foreach ($product_item as $item) {
                    $div .= '<div class="item">';
                    $div .= '<input type="checkbox" name="product_item_ids[]" value="' . $item->id . '" class="my-2" ' . (is_array(old('product_item_id')) && in_array($item->id, old('product_item_ids')) ? ' checked' : '') . '>';
                    $div .= '<span> ' . $item?->product?->name . ' - ' . $item?->name . '</span>';
                    $div .= '<br>';
                    $div .= '</div>';
                }
            });

            return $div;
        });

        View::share('productCategories', ProductConstant::all()->except(ProductConstant::ACCOUNT));
        View::share('productJoki', ProductJoki::all());

        VerifyEmail::toMailUsing(function (User $notifiable) {
            $url = URL::temporarySignedRoute(
                name: 'verification',
                expiration: now()->addMinutes(config('auth.verification.expire')),
                parameters: [
                    'id' => $notifiable->getKey(),
                    'mailhash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            return (new SentVerificationLink(
                user: $notifiable,
                url: $url,
            ));
        });


        ResetPassword::createUrlUsing(function (User $user, string $token) {
            $resetUrl = $user->client->frontend_host . '/reset-password';

            return $resetUrl.'?token='.$token.'&email='.$user->email;
        });
    }
}
