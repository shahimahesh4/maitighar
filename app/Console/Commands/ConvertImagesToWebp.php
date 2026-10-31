<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\GalleryAlbum;
use App\Models\HomeSlider;
use App\Models\MediaAsset;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\User;
use App\Models\VideoAlbum;
use App\Services\ImageOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Throwable;

class ConvertImagesToWebp extends Command
{
    protected $signature = 'images:convert-webp
        {--dry-run : List files that would be converted without changing them}
        {--delete-originals : Delete JPEG and PNG source files after a successful conversion}
        {--skip-database : Do not update image paths stored in the database}';

    protected $description = 'Convert local JPEG and PNG website media to optimized WebP files and update stored media paths';

    public function handle(ImageOptimizationService $optimizer): int
    {
        $roots = [public_path('images'), storage_path('app/public')];
        $files = collect($roots)
            ->filter(fn (string $root): bool => File::isDirectory($root))
            ->flatMap(fn (string $root) => File::allFiles($root))
            ->filter(fn ($file): bool => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png'], true))
            ->values();

        $this->info($files->count().' JPEG/PNG image(s) found.');
        if ($this->option('dry-run')) {
            $files->each(fn ($file) => $this->line($file->getPathname().' → '.preg_replace('/\.[^.]+$/', '.webp', $file->getPathname())));

            return self::SUCCESS;
        }

        $paths = [];
        $converted = 0;
        foreach ($files as $file) {
            $source = $file->getPathname();
            $target = preg_replace('/\.[^.]+$/', '.webp', $source);
            if (! $target) {
                continue;
            }

            if (! is_file($target) && ! $optimizer->convertFileToWebp($source, $target)) {
                $this->warn('Skipped: '.$source);
                continue;
            }

            $paths[$this->relativePath($source)] = $this->relativePath($target);
            $converted++;
            $this->line('WebP: '.$this->relativePath($target));
        }

        $updated = 0;
        if (! $this->option('skip-database') && $paths !== []) {
            try {
                $updated = $this->updateDatabasePaths($paths);
            } catch (Throwable $exception) {
                $this->warn('WebP files were created, but database paths were not updated: '.$exception->getMessage());
                $this->warn('Start the database, then rerun this command to update stored image paths.');
            }
        }

        if ($this->option('delete-originals')) {
            foreach (array_keys($paths) as $path) {
                $absolute = $this->absolutePath($path);
                if ($absolute && is_file($absolute)) {
                    File::delete($absolute);
                }
            }
        }

        $this->info("Created or found {$converted} WebP image(s); updated {$updated} database record(s).".($this->option('delete-originals') ? ' Original files deleted.' : ' Original files retained safely.'));

        return self::SUCCESS;
    }

    /** @param array<string, string> $paths */
    private function updateDatabasePaths(array $paths): int
    {
        $updated = 0;
        $replace = fn (?string $value): ?string => $value && isset($paths[$value]) ? $paths[$value] : $value;
        $replaceArray = fn (?array $values): array => array_map(fn ($value) => is_string($value) ? ($paths[$value] ?? $value) : $value, $values ?? []);

        foreach ([[Product::class, ['main_image'], ['images', 'gallery_images']], [ProductVariation::class, ['image'], []], [Category::class, ['image'], []], [Brand::class, ['image'], []], [Page::class, ['thumbnail_image', 'banner_image'], []], [GalleryAlbum::class, ['cover_image'], ['images']], [HomeSlider::class, ['desktop_image', 'mobile_image'], []], [VideoAlbum::class, ['cover_image'], []], [User::class, ['avatar'], []], [MediaAsset::class, ['path'], []]] as [$class, $scalarFields, $arrayFields]) {
            $class::query()->orderBy('id')->each(function ($model) use (&$updated, $replace, $replaceArray, $scalarFields, $arrayFields, $paths): void {
                $changed = false;
                foreach ($scalarFields as $field) {
                    $newValue = $replace($model->{$field});
                    if ($newValue !== $model->{$field}) {
                        $model->{$field} = $newValue;
                        $changed = true;
                    }
                }
                foreach ($arrayFields as $field) {
                    $newValue = $replaceArray($model->{$field});
                    if ($newValue !== ($model->{$field} ?? [])) {
                        $model->{$field} = $newValue;
                        $changed = true;
                    }
                }
                if ($model instanceof VideoAlbum && is_array($model->videos)) {
                    $videos = array_map(function ($video) use ($paths) {
                        if (is_array($video) && isset($video['thumbnail']) && is_string($video['thumbnail'])) {
                            $video['thumbnail'] = $paths[$video['thumbnail']] ?? $video['thumbnail'];
                        }
                        return $video;
                    }, $model->videos);
                    if ($videos !== $model->videos) {
                        $model->videos = $videos;
                        $changed = true;
                    }
                }
                if ($changed) {
                    $model->saveQuietly();
                    $updated++;
                }
            });
        }

        return $updated;
    }

    private function relativePath(string $path): string
    {
        $public = str_replace('\\', '/', public_path());
        $storage = str_replace('\\', '/', storage_path('app/public'));
        $path = str_replace('\\', '/', $path);

        return str_starts_with($path, $public.'/')
            ? ltrim(substr($path, strlen($public)), '/')
            : ltrim(substr($path, strlen($storage)), '/');
    }

    private function absolutePath(string $path): ?string
    {
        $public = public_path($path);
        if (is_file($public)) {
            return $public;
        }
        $storage = storage_path('app/public/'.$path);

        return is_file($storage) ? $storage : null;
    }
}
