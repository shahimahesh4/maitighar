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

    protected static function booted(): void
    {
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
            $product->on_sale = $product->discount_percentage !== null;
        });
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
}
