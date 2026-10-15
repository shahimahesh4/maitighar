<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $values = Cache::rememberForever(
                'settings.values',
                fn (): array => self::query()->pluck('value', 'key')->all(),
            );
        } catch (\Throwable) {
            // During installation and before test migrations run, the database or
            // settings table may not exist yet. Configuration defaults must still
            // allow Laravel and Artisan to finish booting.
            return $default;
        }
        $value = $values[$key] ?? null;

        if ($value === null) {
            return $default;
        }

        // Attempt to decrypt stored value. If it's not encrypted (legacy), return raw.
        try {
            $decrypted = Crypt::decryptString($value);

            // If decrypted string contains a JSON payload, return decoded value.
            $decoded = json_decode($decrypted, true);
            $resolved = json_last_error() === JSON_ERROR_NONE ? $decoded : $decrypted;

            return $resolved;
        } catch (\Throwable $e) {
            return $value;
        }
    }

    /**
     * Set setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        if ($key === 'store_email' && is_string($value)) {
            $value = strtolower(trim($value));
        }
        // Convert non-string values to JSON before encrypting so arrays/objects are preserved.
        $toStore = is_string($value) ? $value : json_encode($value);

        // Encrypt value before saving to the database.
        $encrypted = Crypt::encryptString($toStore);

        self::updateOrCreate(
            ['key' => $key],
            ['value' => $encrypted]
        );

        Cache::forget('settings.values');
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings.values'));
        static::deleted(fn () => Cache::forget('settings.values'));
    }
}
