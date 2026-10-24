<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products - Maitighar')]

class ProductsPage extends Component
{
    use LivewireAlert;
    use WithPagination;

    #[Url]
    public $selected_categories = [];

    #[Url]
    public $selected_brands = [];

    #[Url]
    public $featured = [];

    #[Url]
    public $on_sale = [];

    #[Url]
    public $price_range = 0;

    #[Url]
    public $sort = 'latest';

    #[Url]
    public $search = '';

    public function clearFilters(): void
    {
        $this->reset([
            'selected_categories',
            'selected_brands',
            'featured',
            'on_sale',
            'price_range',
            'search',
        ]);

        $this->resetPage();
    }

    // Method for adding the product in the cart

    public function addToCart(int $product_id): void
    {
        $total_count = CartManagement::addItemToCart($product_id);

        $this->dispatch('update-cart-count', total_count: $total_count);

        $this->dispatch('product-added-to-cart', product_id: $product_id, quantity: 1);

        $this->alert('success', 'Product added to the cart successfully!', [
            'position' => 'top-end',
            'timer' => 3000,
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
        $highestPrice = (float) Product::query()->where('is_active', true)->max('sale_price');
        $priceMaximum = max(50, (int) (ceil($highestPrice / 10) * 10));
        if ((float) $this->price_range > $priceMaximum) {
            $this->price_range = $priceMaximum;
        }

        $products = Product::query()
            ->withAvg('approvedReviews', 'rating')
            ->where('is_active', true);

        $brands = Brand::where('is_active', 1)->get(['id', 'name', 'slug']);

        $categories = Category::where('is_active', 1)->get(['id', 'name', 'slug']);

        if (! empty($this->selected_categories)) {
            $products->whereIn('category_id', $this->selected_categories);
        }

        if (! empty($this->selected_brands)) {
            $products->whereIn('brand_id', $this->selected_brands);
        }

        if ($this->featured) {
            $products->where('is_featured', 1);
        }

        if ($this->on_sale) {
            $products->where('on_sale', 1);
        }

        if ($this->price_range) {
            $products->whereBetween('sale_price', [0, $this->price_range]);
        }

        if (! empty($this->search)) {
            $products->where('name', 'like', '%'.$this->search.'%');
        }

        if ($this->sort == 'latest') {
            $products->latest();
        }

        if ($this->sort == 'price') {
            $products->orderBy('sale_price');
        }

        $wishlistProductIds = Auth::check()
            ? Wishlist::where('user_id', Auth::id())->pluck('product_id')->all()
            : [];

        return view('livewire.products-page', [
            'products' => $products->paginate(12),
            'brands' => $brands,
            'categories' => $categories,
            'priceMaximum' => $priceMaximum,
            'wishlistProductIds' => $wishlistProductIds,
        ]);
    }
}
