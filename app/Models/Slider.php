<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'autoplay',
        'autoplay_speed',
        'transition_speed',
        'transition_effect',
        'show_navigation',
        'show_pagination',
        'pause_on_hover',
        'desktop_height',
        'tablet_height',
        'mobile_height',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'autoplay' => 'boolean',
        'autoplay_speed' => 'integer',
        'transition_speed' => 'integer',
        'show_navigation' => 'boolean',
        'show_pagination' => 'boolean',
        'pause_on_hover' => 'boolean',
        'settings' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SliderItem::class)->orderBy('sort_order', 'asc');
    }

    public function activeItems(): HasMany
    {
        return $this->items()->where('is_active', true);
    }
}
