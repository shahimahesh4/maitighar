<?php

use App\Support\Money;

if (! function_exists('format_price')) {
    /**
     * Format an amount using AUD as default, with the $ symbol only ($100.00, $1,250.00, $0.00).
     */
    function format_price(int|float|string|null $amount, ?string $currency = null, int $decimals = 2): string
    {
        return Money::format($amount, $currency, $decimals);
    }
}

if (! function_exists('format_price_with_code')) {
    /**
     * Format an amount with an explicit currency code suffix (e.g., Pay $100.00 AUD).
     */
    function format_price_with_code(int|float|string|null $amount, ?string $currency = null, int $decimals = 2): string
    {
        return Money::formatWithCode($amount, $currency, $decimals);
    }
}
