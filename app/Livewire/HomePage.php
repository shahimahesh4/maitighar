<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\HomeSlider;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Wishlist;
use App\Support\Storefront;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
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

        // Keep the Swiper-managed product card DOM intact after a cart update.
        $this->skipRender();
    }

    public function toggleWishlist(int $productId): void
    {
        if (! Storefront::wishlistEnabled()) {
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
        $sliders = HomeSlider::active()->get();

        $new_arrivals = Product::storefront()
            ->with(['brand', 'category'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->latest()
            // Eight products are displayed per desktop carousel page (four columns
            // across two rows); retain another page for the arrow navigation.
            ->take(16)
            ->get();

        $featured_products = Product::storefront()
            ->with(['brand', 'category'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->where('is_featured', true)
            ->latest()
            ->take(12)
            ->get();

        $trending_products = Product::storefront()
            ->with(['brand', 'category'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->where('is_featured', true)
            ->take(3)
            ->get();

        $top_selling_products = Product::storefront()
            ->with(['brand', 'category'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->where('on_sale', true)
            ->take(3)
            ->get();

        $culturalClothing = Category::query()
            ->where(fn ($query) => $query
                ->where('slug', 'cultural-clothing')
                ->orWhere('name', 'Cultural Clothing'))
            ->first();

        // Keep the storefront available during a deployment where application
        // files arrive just before the accompanying database migration.
        $hasAudienceColumn = Schema::hasColumn('categories', 'collection_audience');

        $culturalCollections = $culturalClothing && $hasAudienceColumn
            ? $culturalClothing->children()
                ->where('is_active', true)
                ->whereIn('collection_audience', ['women', 'men', 'kids'])
                ->with(['children' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
                ->get()
                ->keyBy('collection_audience')
            : collect();

        $women_cultural_collection = $culturalCollections->get('women');
        $men_cultural_collection = $culturalCollections->get('men');
        $kids_cultural_collection = $culturalCollections->get('kids');
        $women_cultural_subcategories = $women_cultural_collection?->children ?? collect();
        $men_cultural_subcategories = $men_cultural_collection?->children ?? collect();
        $kids_cultural_subcategories = $kids_cultural_collection?->children ?? collect();

        // Preserve the existing culture cards until the new taxonomy migration
        // has been applied on installations that already have live data.
        $legacy_cultural_subcategories = $culturalClothing
            ? $culturalClothing->children()->where('is_active', true)->orderBy('name')->get()
            : collect();

        $women_cultural_subcategories = $women_cultural_subcategories->isNotEmpty()
            ? $women_cultural_subcategories
            : $legacy_cultural_subcategories;
        $men_cultural_subcategories = $men_cultural_subcategories->isNotEmpty()
            ? $men_cultural_subcategories
            : $legacy_cultural_subcategories;

        $wearer_values = Attribute::query()
            ->where('slug', 'wearer')
            ->where('is_active', true)
            ->first()?->values->keyBy('slug') ?? collect();

        $wishlistProductIds = Auth::check()
            ? Wishlist::where('user_id', Auth::id())->pluck('product_id')->all()
            : [];

        $home = [
            'show_slider' => (bool) Setting::get('home_show_slider', true),
            'show_service_promise' => (bool) Setting::get('home_show_service_promise', true),
            'service_promises' => Setting::get('home_service_promises', [
                ['icon' => 'bi-truck', 'title' => 'Australia-Wide Delivery', 'description' => 'Reliable tracked delivery across Australia.'],
                ['icon' => 'bi-box-seam', 'title' => '7-Day Returns', 'description' => 'Easy return and exchange assistance.'],
                ['icon' => 'bi-credit-card-2-front', 'title' => 'Secure Payment', 'description' => 'Cash on delivery and bank transfer.'],
                ['icon' => 'bi-patch-check', 'title' => 'Authentic Nepali Craft', 'description' => 'Genuine handcrafted heritage items.'],
                ['icon' => 'bi-headset', 'title' => 'Dedicated Support', 'description' => 'Helpful assistance by phone and email.'],
            ]),
            'show_new_arrivals' => (bool) Setting::get('home_show_new_arrivals', true),
            'new_arrivals_title' => Setting::get('home_new_arrivals_title', 'New Arrivals'),
            'show_women_collection' => (bool) Setting::get('home_show_women_collection', true),
            'women_collection_description' => Setting::get('home_women_collection_description', 'Designed to celebrate heritage with modern grace, each piece is finished with the care of Nepali artisans.'),
            'show_featured_products' => (bool) Setting::get('home_show_featured_products', true),
            'featured_products_title' => Setting::get('home_featured_products_title', 'Featured Products'),
            'show_men_collection' => (bool) Setting::get('home_show_men_collection', true),
            'men_collection_description' => Setting::get('home_men_collection_description', 'Crafted for timeless presence, each piece carries the distinction of Nepali tradition and the confidence of modern style.'),
            'show_related_products' => (bool) Setting::get('home_show_related_products', true),
            'related_products_description' => Setting::get('home_related_products_description', 'Explore more handcrafted treasures from our Nepali collection.'),
            'show_highlights' => (bool) Setting::get('home_show_highlights', true),
            'essentials_banner_image' => Setting::get('home_essentials_banner_image', ''),
            'essentials_banner_link' => Setting::get('home_essentials_banner_link', '/products'),
        ];
        $commerceEnabled = Storefront::commerceEnabled();
        $showPrices = Storefront::pricesVisible();
        $quoteLabel = Storefront::quoteLabel();
        $reviewsEnabled = Storefront::reviewsEnabled();
        $wishlistEnabled = Storefront::wishlistEnabled();
        $quickViewEnabled = Storefront::quickViewEnabled();

        return view('livewire.home-page', compact(
            'sliders',
            'new_arrivals',
            'featured_products',
            'wishlistProductIds',
            'trending_products',
            'top_selling_products',
            'women_cultural_subcategories',
            'men_cultural_subcategories',
            'kids_cultural_subcategories',
            'women_cultural_collection',
            'men_cultural_collection',
            'kids_cultural_collection'
            ,'wearer_values', 'home', 'commerceEnabled', 'showPrices', 'quoteLabel', 'reviewsEnabled', 'wishlistEnabled', 'quickViewEnabled'
        ));
    }
}
