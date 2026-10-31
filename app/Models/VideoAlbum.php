<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoAlbum extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'description', 'cover_image', 'videos', 'is_active', 'sort_order', 'published_at'];

    protected function casts(): array
    {
        return ['videos' => 'array', 'is_active' => 'boolean', 'sort_order' => 'integer', 'published_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::saving(function (self $album): void {
            $album->slug = Str::slug($album->slug ?: $album->title);
        });
    }

    public function getRouteKeyName(): string { return 'slug'; }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $builder) => $builder->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->orderBy('sort_order')->orderByDesc('published_at');
    }

    public function getVideoCountAttribute(): int { return count($this->videoItems()); }

    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image) return $this->resolveFileUrl($this->cover_image);
        return collect($this->videoItems())->pluck('thumbnail_url')->filter()->first() ?: asset('images/kba/about-us-academy.webp');
    }

    public function videoItems(): array
    {
        return collect($this->videos ?? [])->map(function (array $video, int $index): ?array {
            $type = ($video['type'] ?? 'youtube') === 'upload' ? 'upload' : 'youtube';
            $title = filled($video['title'] ?? null) ? $video['title'] : $this->title.' — video '.($index + 1);
            if ($type === 'youtube') {
                $id = $this->youtubeId($video['youtube_url'] ?? null);
                if (! $id) return null;
                return ['type' => 'youtube', 'title' => $title, 'source_url' => 'https://www.youtube-nocookie.com/embed/'.$id.'?autoplay=1&rel=0', 'thumbnail_url' => filled($video['thumbnail'] ?? null) ? $this->resolveFileUrl($video['thumbnail']) : 'https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg'];
            }
            if (blank($video['video_file'] ?? null)) return null;
            return ['type' => 'upload', 'title' => $title, 'source_url' => $this->resolveFileUrl($video['video_file']), 'thumbnail_url' => filled($video['thumbnail'] ?? null) ? $this->resolveFileUrl($video['thumbnail']) : asset('images/kba/about-us-academy.webp')];
        })->filter()->values()->all();
    }

    private function youtubeId(?string $url): ?string
    {
        if (blank($url)) return null;
        if (preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{6,})~', $url, $matches)) return $matches[1];
        return preg_match('/^[A-Za-z0-9_-]{6,}$/', $url) ? $url : null;
    }

    private function resolveFileUrl(string $path): string
    {
        if (Str::startsWith($path, ['http://', 'https://'])) return $path;
        if (file_exists(public_path($path))) return asset($path);
        return Storage::disk('public')->url($path);
    }
}
