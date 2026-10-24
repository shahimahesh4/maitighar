<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Category;
use App\Models\HomeSlider;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Maitighar - Nepalese Heritage with Love')]
class HomePage extends Component
{
    use LivewireAlert;

    public function addToCart(int $product_id): void
    {
        $total_count = CartManagement::addItemToCart($product_id);

        $this->dispatch('update-cart-count', total_count: $total_count);

        $this->dispatch('product-added-to-cart', product_id: $product_id, quantity: 1);

        $this->alert('success', 'Product added to your bag.', [
            'position' => 'top-end',
            'timer' => 2500,
            'toast' => true,
        ]);
    }

    public function toggleWishlist(int $productId): void
    {
        if (! Auth::check()) {
            $this->alert('info', 'Please sign in to save items to your wishlist.', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);

            return;
        }

        $userId = Auth::id();
        $wishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $this->dispatch('update-wishlist-count');
            $this->alert('info', 'Removed from wishlist.', [
                'position' => 'top-end',
                'timer' => 2500,
                'toast' => true,
            ]);
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
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
        $sliders = HomeSlider::active()->get();

        $new_arrivals = Product::query()
            ->with(['brand', 'category'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $sale_products = Product::query()
            ->with(['brand', 'category'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->where('is_active', true)
            ->where('on_sale', true)
            ->latest()
            ->take(12)
            ->get();

        $trending_products = Product::query()
            ->with(['brand', 'category'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->take(3)
            ->get();

        if ($trending_products->count() < 3) {
            $trending_products = Product::query()
                ->with(['brand', 'category'])
                ->withAvg('approvedReviews', 'rating')
                ->withCount('approvedReviews')
                ->where('is_active', true)
                ->latest()
                ->take(3)
                ->get();
        }

        $top_selling_products = Product::query()
            ->with(['brand', 'category'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->where('is_active', true)
            ->where('on_sale', true)
            ->take(3)
            ->get();

        if ($top_selling_products->count() < 3) {
            $top_selling_products = Product::query()
                ->with(['brand', 'category'])
                ->withAvg('approvedReviews', 'rating')
                ->withCount('approvedReviews')
                ->where('is_active', true)
                ->oldest()
                ->take(3)
                ->get();
        }

        $culturalClothing = Category::query()
            ->where(fn ($query) => $query
                ->where('slug', 'cultural-clothing')
                ->orWhere('name', 'Cultural Clothing'))
            ->first();

        $cultural_subcategories = $culturalClothing
            ? $culturalClothing->children()
                ->where('is_active', true)
                ->orderBy('name')
                ->take(3)
                ->get()
            : collect();

        $wishlistProductIds = Auth::check()
            ? Wishlist::where('user_id', Auth::id())->pluck('product_id')->all()
            : [];

        return view('livewire.home-page', compact(
            'sliders',
            'new_arrivals',
            'sale_products',
            'wishlistProductIds',
            'trending_products',
            'top_selling_products',
            'cultural_subcategories'
        ));
    }
}
