<?php

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/settings', function () {
        $name = (string) Setting::get('store_name', 'Maitighar');

        return response()->json(['data' => [
            'company_name' => $name,
            'email' => Setting::get('store_email', 'maitigharaustralia@gmail.com'),
            'phone' => Setting::get('store_phone', '+61 432 685 546'),
            'address' => Setting::get('store_address', '2/75 Dartbrook Road, Auburn NSW, Sydney, NSW 2144'),
            'facebook_url' => Setting::get('facebook_url', 'https://www.facebook.com/maitigharaustralia'),
            'instagram_url' => Setting::get('instagram_url'),
        ]]);
    })->name('api.v1.settings');

    Route::get('/products', function () {
        return Product::query()->where('is_active', true)->latest()->paginate(24)->through(fn (Product $product): array => [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'regular_price' => (float) $product->regular_price,
            'sale_price' => (float) $product->sale_price,
            'discount_percentage' => $product->discount_percentage,
            'image_url' => $product->getImageUrl(),
            'in_stock' => (bool) $product->in_stock,
        ]);
    })->name('api.v1.products');
});
