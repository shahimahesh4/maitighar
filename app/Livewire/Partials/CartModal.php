<?php

namespace App\Livewire\Partials;

use App\Helpers\CartManagement;
use App\Models\Product;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CartModal extends Component
{
    public bool $isOpen = false;

    public ?array $product = null;

    public int $quantity = 1;

    public float $subtotal = 0.0;

    public int $total_items = 0;

    #[On('product-added-to-cart')]
    public function showModal(int $product_id, int $quantity = 1): void
    {
        $prod = Product::find($product_id);
        if (! $prod) {
            return;
        }

        $cart_items = CartManagement::getCartItemsFromCookie();

        $salePrice = (float) ($prod->sale_price ?: $prod->price ?: 0);
        $regularPrice = (float) ($prod->regular_price ?: $prod->old_price ?: 0);

        $this->quantity = $quantity;
        $this->product = [
            'id' => $prod->id,
            'name' => $prod->name,
            'slug' => $prod->slug,
            'image' => $prod->getImageUrl(),
            'sale_price' => $salePrice,
            'regular_price' => $regularPrice > $salePrice ? $regularPrice : null,
            'on_sale' => (bool) $prod->on_sale,
        ];

        $this->subtotal = (float) CartManagement::calculateGrandTotal($cart_items);
        $this->total_items = (int) array_sum(array_column($cart_items, 'quantity'));

        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function render(): View
    {
        return view('livewire.partials.cart-modal');
    }
}