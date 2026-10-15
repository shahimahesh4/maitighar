<?php

use App\Services\Payment\Drivers\CodDriver;
use App\Services\Payment\Drivers\EsewaDriver;
use App\Services\Payment\Drivers\FonepayDriver;
use App\Services\Payment\Drivers\KhaltiDriver;
use App\Services\Payment\Drivers\ManualPaymentDriver;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment gateway driver that will be used
    | if no gateway code is explicitly specified.
    |
    */

    'default' => env('PAYMENT_DEFAULT_GATEWAY', 'cod'),

    /*
    |--------------------------------------------------------------------------
    | Registered Payment Gateway Drivers
    |--------------------------------------------------------------------------
    |
    | Define the map of driver keys to their corresponding driver classes.
    | New drivers can be added here without modifying existing business logic.
    |
    */

    'drivers' => [
        'khalti' => KhaltiDriver::class,
        'esewa' => EsewaDriver::class,
        'fonepay' => FonepayDriver::class,
        'manual' => ManualPaymentDriver::class,
        'cod' => CodDriver::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Automatic Retry & Fallback Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for transaction automatic retries and fallback gateway routing
    | when a provider fails.
    |
    */

    'fallback' => [
        'enabled' => true,
        'max_retries' => 3,
        'retry_delay_seconds' => 10,
    ],
];
