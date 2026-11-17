<?php

namespace ITMOSOFT\SellerVerification;

use Illuminate\Support\ServiceProvider;

class SellerVerificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/seller-verification.php', 'itmosoft-seller-verification');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/seller-verification.php' => config_path('itmosoft-seller-verification.php'),
        ], 'seller-verification-config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
