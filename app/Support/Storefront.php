<?php

namespace App\Support;

use App\Models\Setting;

class Storefront
{
    public static function commerceEnabled(): bool
    {
        return Setting::get('storefront_mode', 'quote_only') === 'ecommerce';
    }

    public static function pricesVisible(): bool
    {
        return filter_var(Setting::get('show_product_prices', true), FILTER_VALIDATE_BOOL);
    }

    public static function quoteLabel(): string
    {
        return (string) Setting::get('quote_button_label', 'Request a Quote');
    }

    public static function reviewsEnabled(): bool
    {
        return filter_var(Setting::get('reviews_enabled', true), FILTER_VALIDATE_BOOL);
    }

    public static function wishlistEnabled(): bool
    {
        return filter_var(Setting::get('wishlist_enabled', true), FILTER_VALIDATE_BOOL);
    }

    public static function quickViewEnabled(): bool
    {
        return filter_var(Setting::get('quick_view_enabled', true), FILTER_VALIDATE_BOOL);
    }

    public static function accountSectionEnabled(): bool
    {
        return filter_var(Setting::get('account_section_enabled', true), FILTER_VALIDATE_BOOL);
    }

    public static function loginEnabled(): bool
    {
        return filter_var(Setting::get('login_enabled', true), FILTER_VALIDATE_BOOL);
    }

    public static function registrationEnabled(): bool
    {
        return filter_var(Setting::get('registration_enabled', true), FILTER_VALIDATE_BOOL);
    }
}
