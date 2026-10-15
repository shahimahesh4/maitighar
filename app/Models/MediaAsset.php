<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    protected $fillable = [
        'media_folder_id',
        'disk',
        'path',
        'name',
        'mime_type',
        'size',
        'alt_text',
        'caption',
    ];

    protected static function booted(): void
    {
        static::deleted(function (MediaAsset $asset): void {
            if ($asset->path !== '' && Storage::disk($asset->disk)->exists($asset->path)) {
                Storage::disk($asset->disk)->delete($asset->path);
            }
        });
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'media_folder_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }
}
