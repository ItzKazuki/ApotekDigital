<?php

namespace App\Providers;

use App\Services\Payments\MidtransPaymentService;
use App\Services\Payments\PaymentGatewayInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $paymentMethods = config('payment.list', []);

        $this->app->bind(PaymentGatewayInterface::class, function () use ($paymentMethods) {
            switch (config('payment.gateway_default')) {
                case !empty(array_intersect(['qris'], $paymentMethods)):
                    return new MidtransPaymentService();
                    break;
                // Default fallback
                default:
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
