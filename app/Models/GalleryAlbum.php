<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GalleryAlbum extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'cover_image', 'images',
        'is_active', 'sort_order', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $album): void {
            if (blank($album->slug) && filled($album->title)) {
                $album->slug = Str::slug($album->title);
            }
            $album->slug = Str::slug($album->slug);
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $builder) => $builder->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->orderBy('sort_order')
            ->orderByDesc('published_at');
    }

    public function getImageCountAttribute(): int
    {
        return count(array_filter($this->images ?? []));
    }

    public function getCoverUrlAttribute(): string
    {
        $path = $this->cover_image ?: collect($this->images)->filter()->first();

        return $path ? $this->resolveImageUrl($path) : asset('images/kba/about-us-academy.png');
    }

    public function imageItems(): array
    {
        return collect($this->images ?? [])->filter()->values()->map(fn (string $path, int $index) => [
            'path' => $path,
            'url' => $this->resolveImageUrl($path),
            'alt' => $this->title.' — photo '.($index + 1),
        ])->all();
    }

    private function resolveImageUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (file_exists(public_path($path))) {
            return asset($path);
        }

        $publicDiskUrl = rtrim(
            (string) config('filesystems.disks.public.url', asset('storage')),
            '/',
        );

        return $publicDiskUrl.'/'.ltrim($path, '/');
    }
}
