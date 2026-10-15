<?php

namespace App\Helpers;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class CartManagement
{
    // add item to cart

    public static function addItemToCart($product_id, $qty = 1)
    {
        $cart_items = self::getCartItemsFromCookie();

        $existing_item = null;

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $existing_item = $key;
                break;
            }
        }

        if ($existing_item !== null) {
            $cart_items[$existing_item]['quantity'] += $qty;
            $cart_items[$existing_item]['total_amount'] = $cart_items[$existing_item]['quantity'] * $cart_items[$existing_item]['unit_amount'];
        } else {
            $product = Product::where('id', $product_id)->first(['id', 'name', 'sale_price', 'main_image', 'images']);

            if ($product) {
                $cart_items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->main_image ?: ((! empty($product->images) && isset($product->images[0])) ? $product->images[0] : ''),
                    'quantity' => $qty,
                    'unit_amount' => $product->sale_price,
                    'total_amount' => $product->sale_price * $qty,
                ];
            }
        }

        self::addCartItemsToCookie($cart_items);

        return self::getTotalQuantity();
    }

    // get total quantity of all items in cart
    public static function getTotalQuantity(): int
    {
        $cart_items = self::getCartItemsFromCookie();

        return (int) array_sum(array_column($cart_items, 'quantity'));
    }

    // remove item from cart

    public static function removeCartItems($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                unset($cart_items[$key]);
            }
        }

        $cart_items = array_values($cart_items);

        self::addCartItemsToCookie($cart_items);

        return $cart_items;
    }

    // add cart item to cookie

    public static function addCartItemsToCookie($cart_items)
    {
        $cart_items = array_values($cart_items);
        Cookie::queue('cart_items', json_encode($cart_items), 60 * 24 * 30);
    }

    // clear cart items from cookie

    public static function clearCartItems()
    {
        Cookie::queue('cart_items', json_encode([]), 60 * 24 * 30);
        Cookie::queue(Cookie::forget('cart_items'));
    }

    // get all cart items from cookie

    public static function getCartItemsFromCookie()
    {
        if (Cookie::hasQueued('cart_items')) {
            $queued = Cookie::queued('cart_items');
            $raw = $queued ? $queued->getValue() : null;
            if ($raw !== null && $raw !== '') {
                $items = json_decode($raw, true);
                if (is_array($items)) {
                    return array_values($items);
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

        return array_values($cart_items);
    }

    // increment item quantity

    public static function incrementQuantityToCartItem($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as $key => $item) {

            if ($item['product_id'] == $product_id) {
                $cart_items[$key]['quantity']++;
                $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
            }

        }

        $cart_items = array_values($cart_items);
        self::addCartItemsToCookie($cart_items);

        return $cart_items;
    }

    // decrement item quantity

    public static function decrementQuantityToCartItem($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as $key => $item) {

            if ($item['product_id'] == $product_id) {

                if ($cart_items[$key]['quantity'] > 1) {

                    $cart_items[$key]['quantity']--;

                    $cart_items[$key]['total_amount'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];

                }

            }

        }

        $cart_items = array_values($cart_items);
        self::addCartItemsToCookie($cart_items);

        return $cart_items;

    }

    // calculate grandtotal

    public static function calculateGrandTotal($items)
    {
        return array_sum(array_column($items, 'total_amount'));
    }
}
