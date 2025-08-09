<?php
return [

    /**
     * Show available payment on this project
     * Change as you like it
     */

    'list' => ['tunai'],

    'qris' => [
        'enabled' => env('QRIS_PAYMENT_ENABLED', false),
        'gateway' => env('QRIS_PAYMENT_GATEWAY'), // misal midtras, etc
        'callback' => env('QRIS_PAYMENT_CALLBACK_URL'),
    ]
];
