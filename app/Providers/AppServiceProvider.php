<?php

namespace App\Providers;

use App\Payments\PaymentCodeGenerator;
use App\Payments\UniquePaymentCodeGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PaymentCodeGenerator::class, UniquePaymentCodeGenerator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
