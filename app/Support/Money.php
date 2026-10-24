<?php

namespace App\Support;

use App\Models\Setting;

final class Money
{
    public const DEFAULT_CURRENCY = 'AUD';
    public const DEFAULT_SYMBOL = '$';

    /**
     * Get default currency code from settings.
     */
    public static function defaultCurrency(): string
    {
        try {
            return strtoupper((string) Setting::get('default_currency', self::DEFAULT_CURRENCY));
        } catch (\Throwable) {
            return self::DEFAULT_CURRENCY;
        }
    }

    /**
     * Get current active currency code from session, cookie, or default.
     */
    public static function currentCurrency(): string
    {
        try {
            $currency = session('currency');
            if ($currency && in_array(strtoupper((string) $currency), ['AUD', 'USD', 'NPR', 'EUR', 'GBP'], true)) {
                return strtoupper((string) $currency);
            }

            $cookie = request()->cookie('currency');
            if ($cookie && in_array(strtoupper((string) $cookie), ['AUD', 'USD', 'NPR', 'EUR', 'GBP'], true)) {
                return strtoupper((string) $cookie);
            }

            return self::defaultCurrency();
        } catch (\Throwable) {
            return self::DEFAULT_CURRENCY;
        }
    }

    /**
     * Get currency symbol.
     */
    public static function symbol(?string $currency = null): string
    {
        $curr = strtoupper($currency ?: self::currentCurrency());

        return match ($curr) {
            'AUD', 'USD', 'CAD', 'NZD', 'SGD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'NPR' => 'Rs. ',
            'INR' => '₹',
            default => '$',
        };
    }

    /**
     * Approximate exchange rates relative to 1 AUD for display conversions.
     */
    public static function exchangeRate(string $targetCurrency): float
    {
        $rates = [
            'AUD' => 1.0,
            'USD' => (float) Setting::get('currency_rate_usd', 0.65),
            'NPR' => (float) Setting::get('currency_rate_npr', 87.00),
        ];

        return $rates[strtoupper($targetCurrency)] ?? 1.0;
    }

    /**
     * Convert an AUD amount to the target currency.
     */
    public static function convert(float $amountInAud, ?string $targetCurrency = null): float
    {
        $target = strtoupper($targetCurrency ?: self::currentCurrency());
        if ($target === 'AUD') {
            return $amountInAud;
        }

        $rate = self::exchangeRate($target);

        return round($amountInAud * $rate, 2);
    }

    /**
     * Format an amount using two decimal places and thousands separators.
     * For AUD (the default): displays with '$' symbol only (e.g. $100.00, $1,250.00, $0.00).
     * For alternative currencies: displays appropriate symbol or currency identifier.
     *
     * @param int|float|string|null $amount Amount to format
     * @param string|null $currency Currency code override (if null, uses active currency)
     * @param int $decimals Decimal places (default: 2)
     * @param bool $autoConvert Whether to convert from AUD base price when active currency differs
     */
    public static function format(
        int|float|string|null $amount,
        ?string $currency = null,
        int $decimals = 2,
        bool $autoConvert = true
    ): string {
        $numeric = is_numeric($amount) ? (float) $amount : 0.0;
        $activeCurrency = self::currentCurrency();
        $targetCurrency = strtoupper($currency ?: $activeCurrency);

        // Convert if needed (e.g. browsing in USD or NPR when base prices are in AUD)
        if ($currency === null && $autoConvert && $activeCurrency !== 'AUD') {
            $numeric = self::convert($numeric, $activeCurrency);
        }

        $formatted = number_format($numeric, $decimals, '.', ',');

        // For AUD, display with $ symbol only
        if ($targetCurrency === 'AUD') {
            return '$' . $formatted;
        }

        // For NPR (historical or explicit)
        if ($targetCurrency === 'NPR') {
            return 'Rs. ' . $formatted;
        }

        // For any other alternative currency, clearly identify its currency
        $symbol = self::symbol($targetCurrency);

        return $symbol . $formatted . ' ' . $targetCurrency;
    }

    /**
     * Format an amount with an explicit currency code suffix (e.g., Pay $100.00 AUD).
     */
    public static function formatWithCode(
        int|float|string|null $amount,
        ?string $currency = null,
        int $decimals = 2
    ): string {
        $numeric = is_numeric($amount) ? (float) $amount : 0.0;
        $targetCurrency = strtoupper($currency ?: self::defaultCurrency());
        $formatted = number_format($numeric, $decimals, '.', ',');
        $symbol = self::symbol($targetCurrency);

        return $symbol . $formatted . ' ' . $targetCurrency;
    }
}
