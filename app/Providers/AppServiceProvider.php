<?php

namespace App\Providers;

use App\Services\Payments\MidtransPaymentService;
use App\Services\Payments\PaymentGatewayInterface;
use App\Services\Whatsapp\FonnteService;
use App\Services\Whatsapp\WapiService;
use App\Services\Whatsapp\WhatsappNotificationInterface;
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
            switch (config('payment.default')) {
                case !empty(array_intersect(['qris'], $paymentMethods)):
                    return new MidtransPaymentService();
                    break;
                // Default fallback
                default:
                    return new MidtransPaymentService();
            }
        });

        $this->app->bind(WhatsappNotificationInterface::class, function ($app) {
            $whatsappDriver = env('WHATSAPP_GATEWAY_DRIVER', 'fonnte'); // default fonnte

            return match ($whatsappDriver) {
                'wapi' => new WapiService(),
                'fonnte' => new FonnteService(),
                default  => new FonnteService(),
            };
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
