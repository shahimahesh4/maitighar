<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SliderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'slider_id',
        'title',
        'type', // 'single_image' or 'animated_content'
        'sort_order',
        'is_active',
        'desktop_image',
        'mobile_image',
        'link_url',
        'open_in_new_tab',
        'text_content',
        'buttons',
        'background_settings',
        'layer_animations',
        'responsive_settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
        'sort_order' => 'integer',
        'text_content' => 'array',
        'buttons' => 'array',
        'background_settings' => 'array',
        'layer_animations' => 'array',
        'responsive_settings' => 'array',
    ];

    public function slider(): BelongsTo
    {
        return $this->belongsTo(Slider::class);
    }

    public function isSingleImage(): bool
    {
        return $this->type === 'single_image';
    }

    public function isAnimated(): bool
    {
        return $this->type === 'animated_content';
    }

    public function resolveImageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public function getDesktopImageUrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->desktop_image);
    }

    public function getMobileImageUrlAttribute(): ?string
    {
        return $this->resolveImageUrl($this->mobile_image ?: $this->desktop_image);
    }

    public function getBackgroundImageUrlAttribute(): ?string
    {
        $path = $this->background_settings['image_link'] ?? $this->background_settings['bg_image'] ?? null;
        return $this->resolveImageUrl($path);
    }

    public function getMobileBackgroundImageUrlAttribute(): ?string
    {
        $path = $this->background_settings['mobile_bg_image'] ?? null;
        if (!$path) {
            return $this->getBackgroundImageUrlAttribute();
        }
        return $this->resolveImageUrl($path);
    }

    /**
     * Compute CSS inline background style for the slide.
     */
    public function getBackgroundStyleAttribute(): string
    {
        if ($this->isSingleImage()) {
            return 'background-color: transparent;';
        }

        $bg = $this->background_settings ?? [];
        $type = $bg['type'] ?? 'solid';

        if ($type === 'solid') {
            $color = $bg['bg_color'] ?? '#ebf6ff';
            return "background-color: {$color};";
        }

        if ($type === 'gradient') {
            $from = $bg['gradient_from'] ?? '#ebf6ff';
            $to = $bg['gradient_to'] ?? '#dbeafe';
            $direction = $bg['gradient_direction'] ?? '135deg';
            return "background: linear-gradient({$direction}, {$from}, {$to});";
        }

        if ($type === 'image') {
            $url = $this->getBackgroundImageUrlAttribute();
            $mobileUrl = $this->getMobileBackgroundImageUrlAttribute();
            $pos = $bg['bg_position'] ?? 'center center';
            if ($url) {
                $mobileBackground = $mobileUrl ?: $url;

                return "background-color: " . ($bg['bg_color'] ?? '#ebf6ff') . "; background-image: url('{$url}'); --rev-mobile-background-image: url('{$mobileBackground}'); background-size: cover; background-position: {$pos}; background-repeat: no-repeat;";
            }
            return "background-color: " . ($bg['bg_color'] ?? '#ebf6ff') . ";";
        }

        return 'background-color: #ebf6ff;';
    }
}
