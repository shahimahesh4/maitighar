<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomeSlider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'eyebrow', 'title', 'subtitle', 'desktop_image', 'mobile_image',
        'button_label', 'button_url', 'sort_order', 'duration', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'duration' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public function desktopImageUrl(): string
    {
        return $this->resolveImageUrl($this->desktop_image);
    }

    public function mobileImageUrl(): string
    {
        if (blank($this->mobile_image) && filled($this->desktop_image)) {
            $mobileWebp = preg_replace('/\.[^.]+$/', '-mobile.webp', $this->desktop_image);

            if ($mobileWebp && file_exists(public_path($mobileWebp))) {
                return asset($mobileWebp);
            }
        }

        return $this->resolveImageUrl($this->mobile_image ?: $this->desktop_image);
    }

    private function resolveImageUrl(?string $path): string
    {
        if (blank($path)) {
            return asset('images/kba/beauty-girl.webp');
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (file_exists(public_path($path))) {
            $webpPath = preg_replace('/\.[^.]+$/', '.webp', $path);

            if ($webpPath && file_exists(public_path($webpPath))) {
                return asset($webpPath);
            }

            return asset($path);
        }

        return Storage::disk('public')->url($path);
    }
}
