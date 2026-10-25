<?php

namespace App\Helpers;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Cookie;

class CartManagement
{
    /**
     * Build unique item key for cart item.
     */
    public static function buildItemKey($product_id, $variation_id = null): string
    {
        return $variation_id ? "{$product_id}_{$variation_id}" : (string) $product_id;
    }

    /**
     * Add item to cart (supports simple products and product variations).
     */
    public static function addItemToCart($product_id, $qty = 1, $variation_id = null): int
    {
        $cart_items = self::getCartItemsFromCookie();
        $itemKey = self::buildItemKey($product_id, $variation_id);

        $existing_item = null;
        foreach ($cart_items as $key => $item) {
            $currentKey = $item['item_key'] ?? self::buildItemKey($item['product_id'], $item['variation_id'] ?? null);
            if ($currentKey === $itemKey) {
                $existing_item = $key;
                break;
            }
        }

        if ($existing_item !== null) {
            $cart_items[$existing_item]['quantity'] += (int) $qty;
            $cart_items[$existing_item]['total_amount'] = round(
                $cart_items[$existing_item]['quantity'] * $cart_items[$existing_item]['unit_amount'],
                2
            );
        } else {
            $product = Product::storefront()->find($product_id);
            if (! $product) {
                return self::getTotalQuantity();
            }

            if ($variation_id) {
                $variation = ProductVariation::with(['product', 'variationAttributeValues.attribute', 'variationAttributeValues.attributeValue'])
                    ->where('id', $variation_id)
                    ->where('product_id', $product->id)
                    ->first();

                if (! $variation || ! $variation->is_enabled) {
                    return self::getTotalQuantity();
                }

                $unitPrice = (float) $variation->effective_price;
                $image = $variation->image ?: ($product->main_image ?: ((! empty($product->images) && isset($product->images[0])) ? $product->images[0] : ''));

                $cart_items[] = [
                    'product_id' => $product->id,
                    'variation_id' => (int) $variation->id,
                    'item_key' => $itemKey,
                    'name' => $product->name,
                    'options_text' => $variation->combination_label,
                    'sku' => $variation->sku ?: $product->sku,
                    'image' => $image,
                    'quantity' => (int) $qty,
                    'unit_amount' => $unitPrice,
                    'total_amount' => round($unitPrice * $qty, 2),
                ];
            } else {
                $unitPrice = (float) $product->sale_price;
                $image = $product->main_image ?: ((! empty($product->images) && isset($product->images[0])) ? $product->images[0] : '');

                $cart_items[] = [
                    'product_id' => $product->id,
                    'variation_id' => null,
                    'item_key' => $itemKey,
                    'name' => $product->name,
                    'options_text' => null,
                    'sku' => $product->sku,
                    'image' => $image,
                    'quantity' => (int) $qty,
                    'unit_amount' => $unitPrice,
                    'total_amount' => round($unitPrice * $qty, 2),
                ];
            }
        }

        self::addCartItemsToCookie($cart_items);

        return self::getTotalQuantity();
    }

    /**
     * Get total quantity of all items in cart.
     */
    public static function getTotalQuantity(): int
    {
        $cart_items = self::getCartItemsFromCookie();

        return (int) array_sum(array_column($cart_items, 'quantity'));
    }

    /**
     * Remove item from cart by item_key or product_id.
     */
    public static function removeCartItems($item_id)
    {
        $cart_items = self::getCartItemsFromCookie();
        $targetKey = (string) $item_id;

        foreach ($cart_items as $key => $item) {
            $currentKey = $item['item_key'] ?? self::buildItemKey($item['product_id'], $item['variation_id'] ?? null);
            if ($currentKey === $targetKey || (string) $item['product_id'] === $targetKey && empty($item['variation_id'])) {
                unset($cart_items[$key]);
            }
        }

        $cart_items = array_values($cart_items);
        self::addCartItemsToCookie($cart_items);

        return $cart_items;
    }

    /**
     * Save cart items array to cookie.
     */
    public static function addCartItemsToCookie($cart_items)
    {
        $cart_items = array_values($cart_items);
        Cookie::queue('cart_items', json_encode($cart_items), 60 * 24 * 30);
    }

    /**
     * Clear cart items from cookie.
     */
    public static function clearCartItems()
    {
        Cookie::queue('cart_items', json_encode([]), 60 * 24 * 30);
        Cookie::queue(Cookie::forget('cart_items'));
    }

    /**
     * Get all cart items from cookie.
     */
    public static function getCartItemsFromCookie(): array
    {
        if (Cookie::hasQueued('cart_items')) {
            $queued = Cookie::queued('cart_items');
            $raw = $queued ? $queued->getValue() : null;
            if ($raw !== null && $raw !== '') {
                $items = json_decode($raw, true);
                if (is_array($items)) {
                    return self::normalizeCartItems($items);
                }
            } else {
                return [];
            }
        }

        $rawCookie = Cookie::get('cart_items');
        $cart_items = ! empty($rawCookie) ? json_decode((string) $rawCookie, true) : [];
        if (! is_array($cart_items)) {
            $cart_items = [];
        }

        return self::normalizeCartItems($cart_items);
    }

    /**
     * Normalize cart items to guarantee item_key and options_text.
     */
    private static function normalizeCartItems(array $items): array
    {
        $normalized = [];
        foreach ($items as $item) {
            if (! is_array($item) || empty($item['product_id'])) {
                continue;
            }
            if (empty($item['item_key'])) {
                $item['item_key'] = self::buildItemKey($item['product_id'], $item['variation_id'] ?? null);
            }
            $item['variation_id'] = ! empty($item['variation_id']) ? (int) $item['variation_id'] : null;
            $item['options_text'] = $item['options_text'] ?? null;
            $normalized[] = $item;
        }

        return array_values($normalized);
    }

    /**
     * Increment item quantity.
     */
    public static function incrementQuantityToCartItem($item_id)
    {
        $cart_items = self::getCartItemsFromCookie();
        $targetKey = (string) $item_id;

        foreach ($cart_items as $key => $item) {
            $currentKey = $item['item_key'] ?? self::buildItemKey($item['product_id'], $item['variation_id'] ?? null);
            if ($currentKey === $targetKey || (string) $item['product_id'] === $targetKey && empty($item['variation_id'])) {
                $cart_items[$key]['quantity']++;
                $cart_items[$key]['total_amount'] = round(
                    $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'],
                    2
                );
                break;
            }
        }

        $cart_items = array_values($cart_items);
        self::addCartItemsToCookie($cart_items);

        return $cart_items;
    }

    /**
     * Decrement item quantity.
     */
    public static function decrementQuantityToCartItem($item_id)
    {
        $cart_items = self::getCartItemsFromCookie();
        $targetKey = (string) $item_id;

        foreach ($cart_items as $key => $item) {
            $currentKey = $item['item_key'] ?? self::buildItemKey($item['product_id'], $item['variation_id'] ?? null);
            if ($currentKey === $targetKey || (string) $item['product_id'] === $targetKey && empty($item['variation_id'])) {
                if ($cart_items[$key]['quantity'] > 1) {
                    $cart_items[$key]['quantity']--;
                    $cart_items[$key]['total_amount'] = round(
                        $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'],
                        2
                    );
                }
                break;
            }
        }

        $cart_items = array_values($cart_items);
        self::addCartItemsToCookie($cart_items);

        return $cart_items;
    }

    /**
     * Calculate grand total of all cart items.
     */
    public static function calculateGrandTotal(array $items): float
    {
        return (float) round(array_sum(array_column($items, 'total_amount')), 2);
    }
}
