<?php

namespace App\Console\Commands;

use App\Services\ImageOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateStorefrontWebp extends Command
{
    protected $signature = 'storefront:generate-webp {--max=1600 : Maximum width or height in pixels} {--force : Regenerate existing WebP siblings}';

    protected $description = 'Create optimized WebP siblings for existing public uploads without deleting originals';

    public function handle(ImageOptimizationService $images): int
    {
        $disk = Storage::disk('public');
        $maxDimension = max(320, (int) $this->option('max'));
        $converted = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($disk->allFiles() as $path) {
            if (! preg_match('/\.(?:jpe?g|png)$/i', $path)) {
                continue;
            }

            $target = preg_replace('/\.(?:jpe?g|png)$/i', '.webp', $path);
            if (! $target) {
                $failed++;
                continue;
            }

            if (! $this->option('force') && $disk->exists($target)) {
                $skipped++;
                continue;
            }

            if ($images->convertFileToWebp($disk->path($path), $disk->path($target), $maxDimension)) {
                $converted++;
                $this->line("Converted: {$path}");
            } else {
                $failed++;
                $this->warn("Could not convert: {$path}");
            }
        }

        $this->info("WebP generation complete: {$converted} converted, {$skipped} already optimized, {$failed} failed.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
