<?php

namespace App\Providers;

use App\DiscountTypes\CustomAmountOff;
use App\Modifiers\CustomShippingModifier;
use App\Drivers\FibPaymentDriver;
use App\Drivers\OfflinePaymentDriver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Lunar\Base\CartSessionInterface;
use Lunar\Facades\ModelManifest;
use Lunar\Facades\Payments;
use Lunar\Managers\CartSessionManager;
use Lunar\Models\Product;
use Lunar\Facades\Discounts;
use Lunar\Models\Address;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(\Lunar\Base\ShippingModifiers $shippingModifiers): void
    {
        Discounts::addType(CustomAmountOff::class);

        Payments::extend('fib', function ($app) {
            return $app->make(FibPaymentDriver::class);
        });
        Payments::extend('coffline', function ($app) {
            return $app->make(OfflinePaymentDriver::class);
        });
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
        $this->app->bind(CartSessionInterface::class, CartSessionManager::class);

       $models = collect([
            Product::class => \App\Models\ProductModel::class,
            Address::class => \App\Models\AddressModel::class,
       ]);
       ModelManifest::register($models);
       
    }
}
