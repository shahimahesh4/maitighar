<?php

namespace App\Observers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\GalleryAlbum;
use App\Models\HomeSlider;
use App\Models\MediaAsset;
use App\Models\Page;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\User;
use App\Models\VideoAlbum;
use App\Services\ImageOptimizationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OptimizeModelImages
{
    public function __construct(private readonly ImageOptimizationService $optimizer) {}

    public function saved(Model $model): void
    {
        foreach ($this->paths($model) as [$disk, $path, $maxDimension]) {
            try {
                $this->optimizer->optimizeDiskPath($disk, $path, $maxDimension);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        $this->deleteRemovedImages($model);
    }

    public function deleted(Model $model): void
    {
        foreach ($this->imageReferences($model) as [$disk, $path]) {
            $this->deleteIfUnused($disk, $path, $model);
        }
    }

    private function paths(Model $model): array
    {
        return match (true) {
            $model instanceof Product => $this->fields($model, ['main_image', 'images', 'gallery_images']),
            $model instanceof Category, $model instanceof Brand => $this->fields($model, ['image']),
            $model instanceof Page => $this->fields($model, ['thumbnail_image', 'banner_image']),
            $model instanceof GalleryAlbum => $this->fields($model, ['cover_image', 'images'], 1920),
            $model instanceof HomeSlider => $this->fields($model, ['desktop_image', 'mobile_image']),
            $model instanceof User => $this->fields($model, ['avatar'], 1200),
            $model instanceof MediaAsset => $this->values($model->disk ?: 'public', [$model->path]),
            $model instanceof PaymentTransaction => $this->values('local', [$model->voucher_path]),
            $model instanceof PaymentGateway => $this->values('public', [data_get($model->settings, 'qr_image')]),
            $model instanceof VideoAlbum => $this->values('public', array_merge(
                [$model->cover_image],
                collect($model->videos ?? [])->pluck('thumbnail')->all(),
            )),
            default => [],
        };
    }

    private function fields(Model $model, array $fields, int $maxDimension = 2200): array
    {
        $paths = [];
        foreach ($fields as $field) {
            if (! $model->wasRecentlyCreated && ! $model->wasChanged($field)) {
                continue;
            }
            $value = $model->getAttribute($field);
            $paths = array_merge($paths, $this->values('public', is_array($value) ? $value : [$value], $maxDimension));
        }

        return $paths;
    }

    private function values(string $disk, array $values, int $maxDimension = 2200): array
    {
        return collect($values)
            ->filter(fn ($path) => is_string($path) && filled($path))
            ->map(fn (string $path): array => [$disk, $path, $maxDimension])
            ->all();
    }

    private function deleteRemovedImages(Model $model): void
    {
        if (! $model->wasChanged()) {
            return;
        }

        $current = collect($this->imageReferences($model))->map(fn (array $item): string => $item[0].'|'.$item[1]);

        foreach ($this->imageReferences($model, true) as [$disk, $path]) {
            if (! $current->contains($disk.'|'.$path)) {
                $this->deleteIfUnused($disk, $path, $model);
            }
        }
    }

    private function imageReferences(Model $model, bool $original = false): array
    {
        $fieldValues = function (array $fields, string $disk = 'public') use ($model, $original): array {
            return collect($fields)->flatMap(function (string $field) use ($model, $original, $disk): array {
                $value = $original ? $this->originalValue($model, $field) : $model->getAttribute($field);

                return collect(is_array($value) ? $value : [$value])
                    ->filter(fn ($path): bool => is_string($path) && filled($path))
                    ->map(fn (string $path): array => [$disk, $path])
                    ->all();
            })->all();
        };

        return match (true) {
            $model instanceof Product => $fieldValues(['main_image', 'images', 'gallery_images']),
            $model instanceof Category, $model instanceof Brand => $fieldValues(['image']),
            $model instanceof Page => $fieldValues(['thumbnail_image', 'banner_image']),
            $model instanceof GalleryAlbum => $fieldValues(['cover_image', 'images']),
            $model instanceof HomeSlider => $fieldValues(['desktop_image', 'mobile_image']),
            $model instanceof User => $fieldValues(['avatar']),
            $model instanceof MediaAsset => $fieldValues(['path'], $original ? ($this->originalValue($model, 'disk') ?: 'public') : ($model->disk ?: 'public')),
            $model instanceof VideoAlbum => array_merge(
                $fieldValues(['cover_image']),
                collect($original ? $this->originalValue($model, 'videos') : ($model->videos ?? []))
                    ->pluck('thumbnail')->filter()->map(fn (string $path): array => ['public', $path])->all(),
            ),
            default => [],
        };
    }

    private function originalValue(Model $model, string $field): mixed
    {
        $value = $model->getRawOriginal($field);
        if (is_string($value) && (str_starts_with(ltrim($value), '[') || str_starts_with(ltrim($value), '{'))) {
            return json_decode($value, true) ?? $value;
        }

        return $value;
    }

    private function deleteIfUnused(string $disk, string $path, Model $source): void
    {
        if (blank($path) || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        try {
            if ($this->isReferencedElsewhere($disk, $path, $source)) {
                return;
            }

            if ($disk === 'public' && is_file(public_path($path))) {
                $resolved = realpath(public_path($path));
                $publicRoot = realpath(public_path());
                if ($resolved && $publicRoot && str_starts_with($resolved, $publicRoot.DIRECTORY_SEPARATOR)) {
                    @unlink($resolved);
                }

                return;
            }

            Storage::disk($disk)->delete($path);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function isReferencedElsewhere(string $disk, string $path, Model $source): bool
    {
        $models = [
            Product::class, Category::class, Brand::class, Page::class,
            GalleryAlbum::class, HomeSlider::class, VideoAlbum::class, MediaAsset::class, User::class,
        ];

        foreach ($models as $modelClass) {
            foreach ($modelClass::query()->cursor() as $record) {
                if ($record::class === $source::class && $record->getKey() === $source->getKey()) {
                    continue;
                }
                foreach ($this->imageReferences($record) as [$referenceDisk, $referencePath]) {
                    if ($referenceDisk === $disk && $referencePath === $path) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
