<?php

namespace App\Livewire\Partials;

use App\Helpers\CartManagement;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class QuickViewModal extends Component
{
    use LivewireAlert;

    public bool $isOpen = false;

    public ?Product $product = null;

    public int $quantity = 1;

    public string $selectedImage = '';

    public bool $isWishlisted = false;

    #[On('open-quick-view')]
    public function openModal(int $productId): void
    {
        $product = Product::with(['category', 'brand'])
            ->withAvg('approvedReviews', 'rating')
            ->find($productId);

        if (! $product) {
            return;
        }

        $this->product = $product;
        $this->quantity = 1;
        $this->selectedImage = $product->getImageUrl();
        $this->isWishlisted = auth()->check() && $product->isWishlistedByUser(auth()->id());
        $this->isOpen = true;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->product = null;
        $this->quantity = 1;
        $this->selectedImage = '';
        $this->isWishlisted = false;
    }

    public function selectImage(string $url): void
    {
        $this->selectedImage = $url;
    }

    public function incrementQuantity(): void
    {
        $maxStock = (int) ($this->product?->stock_quantity ?? 99);
        if ($this->quantity < $maxStock) {
            $this->quantity++;
        }
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        if (! $this->product) {
            return;
        }

        $total_count = CartManagement::addItemToCart($this->product->id, $this->quantity);

        $this->dispatch('update-cart-count', total_count: $total_count);
        $this->dispatch('product-added-to-cart', product_id: $this->product->id, quantity: $this->quantity);

        $this->alert('success', "{$this->quantity} item(s) added to your bag.", [
            'position' => 'top-end',
            'timer' => 2500,
            'toast' => true,
        ]);

        $this->closeModal();
    }

    public function toggleWishlist(): void
    {
        if (! $this->product) {
            return;
        }

        if (! auth()->check()) {
            $this->alert('info', 'Please sign in to save items to your wishlist.', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);

            return;
        }

        $userId = auth()->id();
        $wishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $this->product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $this->isWishlisted = false;
            $this->dispatch('update-wishlist-count');
            $this->alert('info', 'Removed from wishlist.', [
                'position' => 'top-end',
                'timer' => 2500,
                'toast' => true,
            ]);
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $this->product->id,
            ]);
            $this->isWishlisted = true;
            $this->dispatch('update-wishlist-count');
            $this->alert('success', 'Saved to your wishlist!', [
                'position' => 'top-end',
                'timer' => 2500,
                'toast' => true,
            ]);
        }
    }

    public function render(): View
    {
        return view('livewire.partials.quick-view-modal');
    }
}
