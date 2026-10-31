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

    // Variation properties
    public bool $isVariable = false;

    public array $variationAttributes = [];

    public array $descriptiveAttributes = [];

    public array $selectedAttributes = [];

    public ?int $selectedVariationId = null;

    public ?string $selectedVariationSku = null;

    public ?float $displayPrice = null;

    public ?float $displayRegularPrice = null;

    public ?int $displayStock = null;

    public bool $isAvailable = true;

    public ?string $variationNotice = null;

    public string $activeImage = '';

    public function mount(string $slug): void
    {
        $this->product = Product::storefront()
            ->where('slug', $slug)
            ->with([
                'approvedReviews.user',
                'category',
                'brand',
                'productAttributes.attribute',
                'productAttributes.values',
                'enabledVariations.variationAttributeValues.attribute',
                'enabledVariations.variationAttributeValues.attributeValue',
            ])
            ->firstOrFail();

        $this->title = $this->product->name.' - Maitighar';
        $this->isVariable = $this->product->isVariable();
        $this->activeImage = $this->product->getImageUrl();

        if ($this->isVariable) {
            $this->loadVariationData();
        } else {
            $this->displayPrice = (float) $this->product->sale_price;
            $this->displayRegularPrice = (float) ($this->product->regular_price ?: $this->product->sale_price);
            $this->displayStock = (int) $this->product->stock_quantity;
            $this->isAvailable = (bool) $this->product->in_stock;
            $this->selectedVariationSku = $this->product->sku;
        }

        // Descriptive attributes visible on product page
        $this->descriptiveAttributes = $this->product->productAttributes
            ->where('is_visible', true)
            ->map(fn ($pa) => [
                'name' => $pa->attribute->name,
                'values' => $pa->values->pluck('value')->implode(', '),
            ])
            ->all();
    }

    private function loadVariationData(): void
    {
        $varAttrs = $this->product->productAttributes
            ->where('is_variation', true)
            ->values();

        $this->variationAttributes = $varAttrs->map(function ($pa) {
            return [
                'id' => $pa->attribute_id,
                'name' => $pa->attribute->name,
                'type' => $pa->attribute->type ?? 'select',
                'values' => $pa->values->map(fn ($v) => [
                    'id' => $v->id,
                    'value' => $v->value,
                    'slug' => $v->slug,
                    'color_code' => $v->color_code,
                ])->all(),
            ];
        })->all();

        $this->selectedAttributes = [];
        $this->selectedVariationId = null;
        $this->selectedVariationSku = $this->product->sku;
        $this->displayPrice = null; // show price range initially
        $this->displayRegularPrice = null;
        $this->displayStock = null;
        $this->isAvailable = true;
        $this->variationNotice = null;
    }

    public function selectAttribute(int $attributeId, int $valueId): void
    {
        $this->selectedAttributes[$attributeId] = $valueId;
        $this->resolveVariation();
    }

    public function resolveVariation(): void
    {
        $requiredCount = count($this->variationAttributes);
        $selectedCount = count($this->selectedAttributes);

        if ($selectedCount < $requiredCount) {
            $this->selectedVariationId = null;
            $this->displayPrice = null;
            $this->displayRegularPrice = null;
            $this->displayStock = null;
            $this->selectedVariationSku = $this->product->sku;
            $this->isAvailable = true;
            $this->variationNotice = 'Please select all available options.';

            return;
        }

        // Search for matching enabled variation
        $matched = null;
        foreach ($this->product->enabledVariations as $var) {
            $varMap = $var->getOptionsMap();
            if ($varMap == $this->selectedAttributes) {
                $matched = $var;
                break;
            }
        }

        if ($matched) {
            $this->selectedVariationId = $matched->id;
            $this->displayPrice = (float) $matched->effective_price;
            $this->displayRegularPrice = (float) ($matched->regular_price ?: $matched->effective_price);
            $this->selectedVariationSku = $matched->sku ?: $this->product->sku;
            $this->displayStock = $matched->manage_stock ? (int) $matched->stock_quantity : 999;
            $this->isAvailable = $matched->isInStock();
            $this->variationNotice = $matched->isInStock() ? null : 'This combination is currently out of stock.';

            if (filled($matched->image)) {
                $this->activeImage = $matched->getImageUrl();
                $this->dispatch('variation-image-changed', image: $this->activeImage);
            }
        } else {
            $this->selectedVariationId = null;
            $this->displayPrice = null;
            $this->displayRegularPrice = null;
            $this->displayStock = 0;
            $this->isAvailable = false;
            $this->variationNotice = 'This combination is currently unavailable.';
        }
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

    public function addToCart(int $product_id): void
    {
        if ($this->isVariable) {
            $requiredCount = count($this->variationAttributes);
            if (count($this->selectedAttributes) < $requiredCount) {
                $this->alert('warning', 'Please select all required options before adding to cart.', [
                    'position' => 'top-end',
                    'timer' => 3000,
                    'toast' => true,
                ]);

                return;
            }

            if (! $this->selectedVariationId) {
                $this->alert('error', 'The selected combination is unavailable.', [
                    'position' => 'top-end',
                    'timer' => 3000,
                    'toast' => true,
                ]);

                return;
            }

            // Server-side stock verification
            $variation = $this->product->enabledVariations->firstWhere('id', $this->selectedVariationId);
            if (! $variation || ! $variation->isInStock()) {
                $this->alert('error', 'This variation is currently out of stock.', [
                    'position' => 'top-end',
                    'timer' => 3000,
                    'toast' => true,
                ]);

                return;
            }

            if ($variation->manage_stock && $variation->stock_quantity < $this->quantity) {
                $this->alert('error', "Only {$variation->stock_quantity} item(s) available in stock.", [
                    'position' => 'top-end',
                    'timer' => 3000,
                    'toast' => true,
                ]);

                return;
            }

            $total_count = CartManagement::addItemToCart($this->product->id, $this->quantity, $this->selectedVariationId);
            $this->dispatch('update-cart-count', total_count: $total_count);
            $this->dispatch('product-added-to-cart', product_id: $this->product->id, quantity: (int) $this->quantity, variation_id: $this->selectedVariationId);
        } else {
            // Simple product stock check
            if ($this->product->stock_quantity > 0 && $this->product->stock_quantity < $this->quantity) {
                $this->alert('error', "Only {$this->product->stock_quantity} item(s) available in stock.", [
                    'position' => 'top-end',
                    'timer' => 3000,
                    'toast' => true,
                ]);

                return;
            }

            $total_count = CartManagement::addItemToCart($this->product->id, $this->quantity);
            $this->dispatch('update-cart-count', total_count: $total_count);
            $this->dispatch('product-added-to-cart', product_id: $this->product->id, quantity: (int) $this->quantity);
        }

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

        $this->reset(['comment']);
        $this->rating = 5;

        $this->product->load('approvedReviews.user');

        $this->alert('success', 'Thank you! Your review has been submitted.', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function toggleWishlist(): void
    {
        if (! auth()->check()) {
            $this->alert('error', 'Please login to manage your wishlist.', [
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
            $this->dispatch('update-wishlist-count');
            $this->alert('info', 'Removed from wishlist!', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        } else {
            Wishlist::create([
                'user_id' => $userId,
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
            'mainImage' => $this->activeImage ?: $this->product->getImageUrl(),
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

        $hasSpecs = !empty($this->descriptiveAttributes)
            || !empty(array_filter($this->product->specifications ?? [], fn ($s) => !empty($s['label']) || !empty($s['value'])));

        if ($hasSpecs) {
            $tabs['specifications'] = 'Product Specifications';
            $content['specifications'] = '';
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
