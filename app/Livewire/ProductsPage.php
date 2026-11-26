<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Brand;
use App\Models\Attribute;
use App\Models\AttributeValue;
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
    public $selected_wearers = [];

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

    public function mount(?Category $category = null, ?AttributeValue $wearer = null): void
    {
        if ($category?->exists) {
            $this->selected_categories = [$category->slug];
        }

        if ($wearer?->exists) {
            abort_unless($wearer->attribute()->where('slug', 'wearer')->exists(), 404);

            $this->selected_wearers = [$wearer->slug];
        }
    }

    public function clearFilters(): void
    {
        $this->reset([
            'selected_categories',
            'selected_brands',
            'selected_wearers',
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

    }

    public function toggleWishlist(int $productId): void
    {
        if (! \App\Support\Storefront::wishlistEnabled()) {
            return;
        }

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
        $highestPrice = (float) Product::storefront()->max('sale_price');
        $priceMaximum = max(50, (int) (ceil($highestPrice / 10) * 10));
        if ((float) $this->price_range > $priceMaximum) {
            $this->price_range = $priceMaximum;
        }

        $products = Product::storefront()
            ->withAvg('approvedReviews', 'rating');

        $brands = Brand::where('is_active', 1)->get(['id', 'name', 'slug']);

        $categories = Category::query()
            ->where('is_active', 1)
            ->whereNull('parent_id')
            ->with(['children' => fn ($query) => $query
                ->where('is_active', 1)
                ->orderBy('name')])
            ->orderBy('name')
            ->get(['id', 'parent_id', 'name', 'slug']);
        $wearerAttribute = Attribute::query()
            ->where('slug', 'wearer')
            ->where('is_active', true)
            ->with('values')
            ->first();

        if (! empty($this->selected_categories)) {
            // selected_categories contains slugs — resolve to IDs first
            $categoryIds = Category::where('is_active', 1)
                ->whereIn('slug', $this->selected_categories)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
            if (! empty($categoryIds)) {
                $products->whereIn('category_id', Category::idsIncludingDescendants($categoryIds));
            }
        }

        if (! empty($this->selected_brands)) {
            // selected_brands contains slugs — resolve to IDs first
            $brandIds = Brand::where('is_active', 1)
                ->whereIn('slug', $this->selected_brands)
                ->pluck('id')
                ->all();
            if (! empty($brandIds)) {
                $products->whereIn('brand_id', $brandIds);
            }
        }

        if ($wearerAttribute && ! empty($this->selected_wearers)) {
            // selected_wearers contains slugs — resolve to IDs for the query
            $wearerIds = $wearerAttribute->values
                ->whereIn('slug', $this->selected_wearers)
                ->pluck('id')
                ->all();

            if (! empty($wearerIds)) {
                $products->whereHas('productAttributes', fn ($query) => $query
                    ->where('attribute_id', $wearerAttribute->id)
                    ->whereHas('values', fn ($values) => $values->whereIn('attribute_values.id', $wearerIds)));
            }
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
            'wearerAttribute' => $wearerAttribute,
            'priceMaximum' => $priceMaximum,
            'wishlistProductIds' => $wishlistProductIds,
        ]);
    }
}
