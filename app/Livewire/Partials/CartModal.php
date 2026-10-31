<?php

namespace App\Livewire\Partials;

use App\Helpers\CartManagement;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Storage;
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

    /** @var array<int, array<string, mixed>> */
    public array $cartItems = [];

    #[On('product-added-to-cart')]
    public function showModal(int $product_id, int $quantity = 1, ?int $variation_id = null): void
    {
        $prod = Product::storefront()->find($product_id);
        if (! $prod) {
            return;
        }

        $cart_items = CartManagement::getCartItemsFromCookie();

        $salePrice = (float) ($prod->sale_price ?: $prod->price ?: 0);
        $regularPrice = (float) ($prod->regular_price ?: $prod->old_price ?: 0);
        $image = $prod->getImageUrl();
        $optionsText = null;

        if ($variation_id) {
            $variation = ProductVariation::with('variationAttributeValues.attribute', 'variationAttributeValues.attributeValue')
                ->where('id', $variation_id)
                ->where('product_id', $prod->id)
                ->first();

            if ($variation) {
                $salePrice = (float) $variation->effective_price;
                $regularPrice = (float) ($variation->regular_price ?: $salePrice);
                $image = $variation->getImageUrl();
                $optionsText = $variation->combination_label;
            }
        }

        $this->quantity = $quantity;
        $this->product = [
            'id' => $prod->id,
            'variation_id' => $variation_id,
            'name' => $prod->name,
            'options_text' => $optionsText,
            'slug' => $prod->slug,
            'image' => $image,
            'sale_price' => $salePrice,
            'regular_price' => $regularPrice > $salePrice ? $regularPrice : null,
            'on_sale' => (bool) $prod->on_sale,
        ];

        $this->subtotal = (float) CartManagement::calculateGrandTotal($cart_items);
        $this->total_items = (int) array_sum(array_column($cart_items, 'quantity'));
        $this->cartItems = array_map(fn (array $item): array => [
            ...$item,
            'image_url' => $this->resolveImageUrl($item['image'] ?? null),
        ], $cart_items);

        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
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

    public function render(): View
    {
        return view('livewire.partials.cart-modal');
    }
}
