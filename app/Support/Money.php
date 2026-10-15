<?php

namespace App\Support;

final class Money
{
    public static function format(int|float|string|null $amount, int $decimals = 2): string
    {
        $value = is_numeric($amount) ? (float) $amount : 0.0;

        return 'Rs. '.number_format($value, $decimals, '.', ',');
    }
}
