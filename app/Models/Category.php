<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'parent_id',
        'collection_audience',
        'name',
        'slug',
        'image',
        'is_active',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Expand selected category IDs to include every nested child category.
     *
     * This keeps a collection link such as "Women's Cultural Dresses" useful:
     * visitors see products assigned to any of its cultural subcategories.
     */
    public static function idsIncludingDescendants(array $categoryIds): array
    {
        $ids = collect($categoryIds)
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $frontier = $ids;

        while ($frontier->isNotEmpty()) {
            $children = static::query()
                ->whereIn('parent_id', $frontier)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->reject(fn ($id) => $ids->contains($id))
                ->values();

            if ($children->isEmpty()) {
                break;
            }

            $ids = $ids->merge($children)->unique()->values();
            $frontier = $children;
        }

        return $ids->all();
    }

    public function getImageUrl()
    {
        if (empty($this->image)) {
            return 'https://placehold.co/400x400/3b82f6/ffffff?text='.urlencode($this->name);
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'images/')) {
            return asset($this->image);
        }

        return url('storage/'.$this->image);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('navigation.categories'));
        static::deleted(fn () => Cache::forget('navigation.categories'));
    }
}
