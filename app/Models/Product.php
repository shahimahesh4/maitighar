<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'product_type',
        'slug',
        'images',
        'main_image',
        'gallery_images',
        'description',
        'short_description',
        'price',
        'old_price',
        'regular_price',
        'sale_price',
        'discount_percentage',
        'stock_quantity',
        'specifications',
        'delivery_policy',
        'shipping_returns',
        'custom_tab_title',
        'custom_tab_content',
        'product_tabs',
        'sku',
        'is_active',
        'is_featured',
        'in_stock',
        'on_sale',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'images' => 'array',
        'gallery_images' => 'array',
        'specifications' => 'array',
        'product_tabs' => 'array',
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'discount_percentage' => 'integer',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'in_stock' => 'boolean',
        'on_sale' => 'boolean',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrl(): string
    {
        $image = $this->main_image ?: ($this->images[0] ?? null);

        return $this->resolveImageUrl($image);
    }

    public function getThumbnailUrl(): string
    {
        $image = $this->main_image ?: ($this->images[0] ?? null);

        if (filled($image) && str_starts_with($image, 'images/')) {
            $thumbnail = preg_replace('/\.[^.]+$/', '-thumb.webp', $image);

            if ($thumbnail && file_exists(public_path($thumbnail))) {
                return asset($thumbnail);
            }
        }

        return $this->resolveImageUrl($image);
    }

    public function getGalleryImages(): array
    {
        if (! empty($this->gallery_images)) {
            return array_values(array_filter($this->gallery_images));
        }

        return array_values(array_slice($this->images ?? [], 1));
    }

    public function getGalleryImageUrls(): array
    {
        $images = array_filter(array_merge([
            $this->main_image ?: ($this->images[0] ?? null),
        ], $this->getGalleryImages()));

        return array_values(array_unique(array_map(
            fn (string $image): string => $this->resolveImageUrl($image),
            $images,
        )));
    }

    /**
     * Generate the next sequential SKU based on the store's configured prefix.
     * E.g. MG-AUS-001, MG-AUS-002, MG-AUS-003, ...
     */
    public static function generateNextSku(?string $prefix = null): string
    {
        $prefix = $prefix ?: (string) Setting::get('sku_prefix', 'MG-AUS-');

        $skus = static::query()
            ->where('sku', 'like', $prefix . '%')
            ->pluck('sku');

        $maxNumber = 0;
        foreach ($skus as $sku) {
            if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', (string) $sku, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxNumber) {
                    $maxNumber = $num;
                }
            }
        }

        $nextNumber = $maxNumber + 1;
        do {
            $candidate = $prefix . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
            $nextNumber++;
        } while (static::where('sku', $candidate)->exists());

        return $candidate;
    }

    protected static function booted(): void
    {
        static::creating(function (self $product): void {
            if (blank($product->sku)) {
                $product->sku = static::generateNextSku();
            }
        });

        static::saving(function (self $product): void {
            $salePrice = (float) ($product->sale_price ?? $product->price ?? 0);
            $regularPrice = (float) ($product->regular_price ?? $product->old_price ?? $salePrice);
            $product->sale_price = $salePrice;
            $product->regular_price = max($regularPrice, $salePrice);
            $product->discount_percentage = $product->regular_price > $salePrice && $product->regular_price > 0
                ? (int) round((($product->regular_price - $salePrice) / $product->regular_price) * 100)
                : null;
            $product->price = $product->sale_price;
            $product->old_price = $product->discount_percentage ? $product->regular_price : null;
        });
    }

    /**
     * Limit a query to products that customers can browse and purchase.
     */
    public function scopeStorefront($query)
    {
        return $query
            ->where('is_active', true)
            ->where('in_stock', true);
    }

    public function resolveImageUrl(?string $image): string
    {
        if (blank($image)) {
            return 'https://placehold.co/600x600/b79252/ffffff?text='.urlencode($this->name);
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        if (str_starts_with($image, '/')) {
            return url($image);
        }

        if (str_starts_with($image, 'images/')) {
            return asset($image);
        }

        return asset('storage/'.$image);
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the Order Items that owns the product.
     */
    public function orderItems()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function getAverageRatingAttribute(): float
    {
        if (array_key_exists('approved_reviews_avg_rating', $this->attributes)) {
            return round((float) ($this->attributes['approved_reviews_avg_rating'] ?? 0), 1);
        }

        if ($this->relationLoaded('approvedReviews')) {
            return round((float) ($this->approvedReviews->avg('rating') ?? 0), 1);
        }

        return round((float) ($this->approvedReviews()->avg('rating') ?? 0), 1);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function isWishlistedByUser($userId)
    {
        if (! $userId) {
            return false;
        }

        return $this->wishlists()->where('user_id', $userId)->exists();
    }

    public function faqs()
    {
        return $this->hasMany(ProductFaq::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class)->orderBy('sort_order')->orderBy('id');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes')
            ->withPivot(['id', 'is_visible', 'is_variation', 'sort_order'])
            ->withTimestamps()
            ->orderBy('product_attributes.sort_order')
            ->orderBy('product_attributes.id');
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class)->orderBy('sort_order')->orderBy('id');
    }

    public function enabledVariations()
    {
        return $this->hasMany(ProductVariation::class)->where('is_enabled', true)->orderBy('sort_order')->orderBy('id');
    }

    public function isSimple(): bool
    {
        return ($this->product_type ?? 'simple') === 'simple';
    }

    public function isVariable(): bool
    {
        return ($this->product_type ?? 'simple') === 'variable';
    }

    public function getMinPriceAttribute(): float
    {
        if ($this->isVariable()) {
            $min = $this->enabledVariations->min(fn ($v) => (float) ($v->sale_price ?: $v->regular_price ?: 0));
            if ($min !== null && $min > 0) {
                return (float) $min;
            }
        }

        return (float) ($this->sale_price ?: $this->price ?: 0);
    }

    public function getMaxPriceAttribute(): float
    {
        if ($this->isVariable()) {
            $max = $this->enabledVariations->max(fn ($v) => (float) ($v->sale_price ?: $v->regular_price ?: 0));
            if ($max !== null && $max > 0) {
                return (float) $max;
            }
        }

        return (float) ($this->sale_price ?: $this->price ?: 0);
    }

    public function hasPriceRange(): bool
    {
        if (! $this->isVariable()) {
            return false;
        }

        $min = $this->min_price;
        $max = $this->max_price;

        return $min > 0 && $max > 0 && round($min, 2) !== round($max, 2);
    }

    public function getFormattedPriceRange(): string
    {
        if ($this->isVariable()) {
            $min = $this->min_price;
            $max = $this->max_price;

            if ($min > 0 && $max > 0 && round($min, 2) !== round($max, 2)) {
                return \App\Support\Money::format($min) . ' – ' . \App\Support\Money::format($max);
            }

            if ($min > 0) {
                return \App\Support\Money::format($min);
            }
        }

        return \App\Support\Money::format($this->sale_price);
    }

    public function syncPriceAndStockFromVariations(): void
    {
        if (! $this->isVariable()) {
            return;
        }

        $variations = $this->variations()->where('is_enabled', true)->get();
        if ($variations->isEmpty()) {
            return;
        }

        $minSale = $variations->min(fn ($v) => (float) ($v->sale_price ?: $v->regular_price ?: 0));
        $maxRegular = $variations->max(fn ($v) => (float) ($v->regular_price ?: $v->sale_price ?: 0));
        $totalStock = $variations->sum('stock_quantity');

        $this->sale_price = $minSale ?: $this->sale_price;
        $this->regular_price = $maxRegular ?: $this->regular_price;
        $this->price = $this->sale_price;
        $this->stock_quantity = $totalStock;
        $this->in_stock = $totalStock > 0 || $variations->contains(fn ($v) => ! $v->manage_stock);

        $this->saveQuietly();
    }
}

