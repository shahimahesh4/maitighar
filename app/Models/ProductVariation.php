<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ProductVariation extends Model
{
    use HasFactory;

    protected $table = 'product_variations';

    protected $fillable = [
        'product_id',
        'sku',
        'regular_price',
        'sale_price',
        'stock_quantity',
        'manage_stock',
        'image',
        'is_enabled',
        'sort_order',
    ];

    protected $casts = [
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'manage_stock' => 'boolean',
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $variation): void {
            $salePrice = (float) ($variation->sale_price ?? 0);
            $regularPrice = (float) ($variation->regular_price ?? $salePrice);
            if ($salePrice > 0 && $regularPrice < $salePrice) {
                $variation->regular_price = $salePrice;
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variationAttributeValues(): HasMany
    {
        return $this->hasMany(ProductVariationAttributeValue::class, 'product_variation_id');
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_variation_attribute_values',
            'product_variation_id',
            'attribute_value_id'
        )->withPivot('attribute_id')->withTimestamps();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'variation_id');
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(InventoryLog::class, 'variation_id');
    }

    public function isInStock(): bool
    {
        if (! $this->is_enabled) {
            return false;
        }

        if (! $this->manage_stock) {
            return true;
        }

        return $this->stock_quantity > 0;
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->sale_price !== null && (float) $this->sale_price > 0) {
            return (float) $this->sale_price;
        }

        if ($this->regular_price !== null && (float) $this->regular_price > 0) {
            return (float) $this->regular_price;
        }

        return (float) ($this->product?->sale_price ?? 0);
    }

    public function getCombinationLabelAttribute(): string
    {
        $this->loadMissing('variationAttributeValues.attribute', 'variationAttributeValues.attributeValue');

        $pairs = [];
        foreach ($this->variationAttributeValues as $vav) {
            $attrName = $vav->attribute?->name ?? 'Option';
            $valName = $vav->attributeValue?->value ?? '-';
            $pairs[] = "{$attrName}: {$valName}";
        }

        return $pairs !== [] ? implode(' / ', $pairs) : 'Default Variation';
    }

    public function getOptionsMap(): array
    {
        $this->loadMissing('variationAttributeValues.attribute', 'variationAttributeValues.attributeValue');

        $map = [];
        foreach ($this->variationAttributeValues as $vav) {
            if ($vav->attribute_id && $vav->attribute_value_id) {
                $map[$vav->attribute_id] = (int) $vav->attribute_value_id;
            }
        }

        return $map;
    }

    public function getImageUrl(): string
    {
        if (filled($this->image)) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }
            if (str_starts_with($this->image, '/')) {
                return url($this->image);
            }
            if (str_starts_with($this->image, 'images/')) {
                $webpImage = preg_replace('/\.(?:jpe?g|png)$/i', '.webp', $this->image);
                if ($webpImage && is_file(public_path($webpImage))) {
                    return asset($webpImage);
                }

                return asset($this->image);
            }

            $webpImage = preg_replace('/\.(?:jpe?g|png)$/i', '.webp', $this->image);
            if ($webpImage && Storage::disk('public')->exists($webpImage)) {
                return Storage::disk('public')->url($webpImage);
            }

            return Storage::disk('public')->url($this->image);
        }

        return $this->product?->getImageUrl() ?? '';
    }
}
