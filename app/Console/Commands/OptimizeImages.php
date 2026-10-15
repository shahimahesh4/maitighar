<?php

namespace App\Console\Commands;

use App\Services\ImageOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize {--dry-run : List eligible files without changing them} {--path= : Limit processing to a directory}';

    protected $description = 'Resize and compress JPEG, PNG, and WebP images in place';

    public function handle(ImageOptimizationService $optimizer): int
    {
        $roots = $this->option('path')
            ? [base_path((string) $this->option('path'))]
            : [public_path('images'), storage_path('app/public'), storage_path('app/private')];
        $files = collect($roots)
            ->filter(fn (string $root): bool => File::isDirectory($root))
            ->flatMap(fn (string $root) => File::allFiles($root))
            ->filter(fn ($file): bool => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true));

        if ($this->option('dry-run')) {
            $files->each(fn ($file) => $this->line($file->getPathname()));
            $this->info($files->count().' eligible images found. No files were changed.');

            return self::SUCCESS;
        }

        $optimized = 0;
        $before = 0;
        $after = 0;
        foreach ($files as $file) {
            $path = $file->getPathname();
            $originalSize = filesize($path) ?: 0;
            $before += $originalSize;
            if ($optimizer->optimizeFile($path)) {
                $optimized++;
                $this->line('Optimized: '.$path);
            }
            clearstatcache(true, $path);
            $after += filesize($path) ?: $originalSize;
        }

        $saved = max(0, $before - $after);
        $this->info("Optimized {$optimized} of {$files->count()} images; saved ".number_format($saved / 1048576, 2).' MB.');

        return self::SUCCESS;
    }
}
