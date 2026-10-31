<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Cart - Maitighar')]
class CartPage extends Component
{
    public array $cart_items = [];

    public float $grand_total = 0;

    public function mount(): void
    {
        $this->refreshCart(CartManagement::getCartItemsFromCookie());
    }

    public function incrementQty(string|int $item_id): void
    {
        $this->refreshCart(CartManagement::incrementQuantityToCartItem($item_id), true);
    }

    public function decrementQty(string|int $item_id): void
    {
        $this->refreshCart(CartManagement::decrementQuantityToCartItem($item_id), true);
    }

    public function removeItem(string|int $item_id): void
    {
        $this->refreshCart(CartManagement::removeCartItems($item_id), true);
    }

    public function render(): View
    {
        return view('livewire.cart-page', [
            'cartQuantity' => collect($this->cart_items)->sum('quantity'),
            'cartItems' => collect($this->cart_items)
                ->map(fn (array $item): array => [
                    ...$item,
                    'image_url' => $this->resolveImageUrl($item['image'] ?? null),
                ])
                ->all(),
        ]);
    }

    private function refreshCart(array $items, bool $dispatchCount = false): void
    {
        $this->cart_items = $items;
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);

        if ($dispatchCount) {
            $this->dispatch('update-cart-count', total_count: CartManagement::getTotalQuantity());
        }
    }

    private function resolveImageUrl(?string $image): string
    {
        return match (true) {
            blank($image) => asset('images/logo.webp'),
            str_starts_with($image, 'http://'), str_starts_with($image, 'https://') => $image,
            str_starts_with($image, '/') => url($image),
            str_starts_with($image, 'images/') => asset($image),
            default => Storage::disk('public')->url($image),
        };
    }
}
