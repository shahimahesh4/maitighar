<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use App\Models\PageFaq;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'about_info_boxes',
        'thumbnail_image',
        'banner_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'about_info_boxes' => 'array',
        'sort_order' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function faqs()
    {
        return $this->hasMany(PageFaq::class)->orderBy('sort_order');
    }

    public function getThumbnailUrl(): string
    {
        if (! filled($this->thumbnail_image)) {
            return asset('images/logo.webp');
        }

        if (str_starts_with($this->thumbnail_image, 'http://') || str_starts_with($this->thumbnail_image, 'https://')) {
            return $this->thumbnail_image;
        }

        if (file_exists(public_path($this->thumbnail_image))) {
            return asset($this->thumbnail_image);
        }

        return asset('storage/'.$this->thumbnail_image);
    }

    public function getBannerUrl(): string
    {
        if (! filled($this->banner_image)) {
            return $this->getThumbnailUrl();
        }

        if (str_starts_with($this->banner_image, 'http://') || str_starts_with($this->banner_image, 'https://')) {
            return $this->banner_image;
        }

        if (file_exists(public_path($this->banner_image))) {
            return asset($this->banner_image);
        }

        return asset('storage/'.$this->banner_image);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->orderBy('sort_order')->orderBy('id');
    }

    protected static function booted(): void
    {
        static::saving(function (self $page): void {
            if (blank($page->slug) && filled($page->title)) {
                $page->slug = Str::slug($page->title);
            }

            if (filled($page->slug)) {
                $page->slug = Str::slug($page->slug);
            }
        });
    }
}
