<?php
$qrisEnabled = env('QRIS_PAYMENT_ENABLED', false);

$paymentList = ['tunai']; // default
if ($qrisEnabled) {
    $paymentList[] = 'qris';
}

return [

    /**
     * Show available payment on this project
     * Change as you like it
     */
    'list' => $paymentList,

    'default' => env('PAYMENT_GATEWAY', 'midtrans'),

    'midtrans' => [
        'server_key' => env('PAYMENT_MIDTRANS_SERVER_KEY'),
        'client_key' => env('PAYMENT_MIDTRANS_CLIENT_KEY'),
        'is_production' => env('PAYMENT_MIDTRANS_IS_PRODUCTION', false),
        'api_sandbox_url' => env('PAYMENT_MIDTRANS_API_URL', 'https://api.sandbox.midtrans.com/v2/charge'), # docs https://docs.midtrans.com/reference/https-request-1
        'callback' => env('PAYMENT_MIDTRANS_CALLBACK_URL'),
        'qris' => [
            'enabled' => $qrisEnabled,
            'expired_after' => 15 # menit
        ],
    ],
];
