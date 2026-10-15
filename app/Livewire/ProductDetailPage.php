<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use App\Models\Review;
use App\Models\Wishlist;
use App\Traits\GoogleRecaptcha;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ProductDetailPage extends Component
{
    use GoogleRecaptcha;
    use LivewireAlert;

    public $product;

    public $title;

    public $quantity = 1;

    // Review fields
    public $rating = 5;

    public $comment;

    public function mount(string $slug): void
    {
        $this->product = Product::where('slug', $slug)->with('approvedReviews.user')->firstOrFail();
        $this->title = $this->product->name.' - Maitighar';
    }

    public function increaseQty(): void
    {
        $this->quantity++;
    }

    public function decreaseQty(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    // Method for adding the product in the cart

    public function addToCart(int $product_id): void
    {
        $total_count = CartManagement::addItemToCart($product_id, $this->quantity);

        $this->dispatch('update-cart-count', total_count: $total_count);

        $this->dispatch('product-added-to-cart', product_id: $product_id, quantity: (int) $this->quantity);

        $this->alert('success', 'Product added to the cart successfully!', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);

    }

    public function submitReview(): void
    {
        if (! auth()->check()) {
            $this->alert('error', 'Please login to leave a review.', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);

            return;
        }

        $rules = array_merge($this->getRecaptchaRules(), [
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $this->validate($rules);

        $existing = Review::where('user_id', auth()->id())
            ->where('product_id', $this->product->id)
            ->first();

        if ($existing) {
            $this->alert('warning', 'You have already reviewed this product.', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);

            return;
        }

        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $this->product->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'is_approved' => true,
        ]);

        $this->comment = '';
        $this->rating = 5;

        // Reload the product relationships
        $this->product->load('approvedReviews.user');

        $this->alert('success', 'Thank you! Your review has been posted.', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function toggleWishlist(): void
    {
        if (! auth()->check()) {
            $this->alert('info', 'Please log in to add items to your wishlist.', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);

            return;
        }

        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $this->product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->dispatch('update-wishlist-count');
            $this->alert('success', 'Removed from wishlist.', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $this->product->id,
            ]);
            $this->dispatch('update-wishlist-count');
            $this->alert('success', 'Added to wishlist!', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        }

        $this->product->load('wishlists');
    }

    public function render(): View
    {
        $averageRating = $this->product->average_rating;
        $fullStars = (int) floor($averageRating);
        $hasHalfStar = ($averageRating - $fullStars) >= 0.5;
        [$detailTabs, $detailTabContent] = $this->detailTabs();

        return view('livewire.product-detail-page', [
            'product' => $this->product,
            'mainImage' => $this->product->getImageUrl(),
            'productGallery' => $this->product->getGalleryImageUrls(),
            'detailTabs' => $detailTabs,
            'detailTabContent' => $detailTabContent,
            'initialTab' => array_key_first($detailTabs) ?: 'description',
            'fullStars' => $fullStars,
            'hasHalfStar' => $hasHalfStar,
            'emptyStars' => 5 - $fullStars - ($hasHalfStar ? 1 : 0),
            'ratingDistribution' => $this->ratingDistribution(),
            'isWishlisted' => auth()->check() && $this->product->isWishlistedByUser(auth()->id()),
        ])
            ->layoutData(['title' => $this->title]);
    }

    private function detailTabs(): array
    {
        $tabs = [];
        $content = [];

        if ($this->product->description) {
            $tabs['description'] = 'Description';
            $content['description'] = preg_match('/<[^>]+>/', $this->product->description)
                ? $this->product->description
                : Str::markdown($this->product->description);
        }

        $productTabs = $this->product->product_tabs ?? [];

        if ($productTabs === []) {
            $productTabs = array_values(array_filter([
                $this->product->delivery_policy ? ['title' => 'Delivery Policy', 'content' => nl2br(e($this->product->delivery_policy))] : null,
                $this->product->shipping_returns ? ['title' => 'Shipping & Returns', 'content' => nl2br(e($this->product->shipping_returns))] : null,
                ($this->product->custom_tab_title && $this->product->custom_tab_content)
                    ? ['title' => $this->product->custom_tab_title, 'content' => nl2br(e($this->product->custom_tab_content))]
                    : null,
            ]));
        }

        foreach ($productTabs as $index => $tab) {
            if (! filled($tab['title'] ?? null) || ! filled($tab['content'] ?? null)) {
                continue;
            }

            $key = 'product-tab-'.$index;
            $tabs[$key] = $tab['title'];
            $content[$key] = $tab['content'];
        }

        return [$tabs, $content];
    }

    private function ratingDistribution(): array
    {
        $reviewCount = $this->product->approvedReviews->count();

        return collect(range(5, 1))
            ->mapWithKeys(fn (int $stars): array => [
                $stars => $reviewCount === 0
                    ? 0
                    : (int) round(($this->product->approvedReviews->where('rating', $stars)->count() / $reviewCount) * 100),
            ])
            ->all();
    }
}
