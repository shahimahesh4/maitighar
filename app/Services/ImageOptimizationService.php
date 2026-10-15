<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageOptimizationService
{
    public function optimizeDiskPath(string $disk, ?string $path, int $maxDimension = 2200): bool
    {
        if (blank($path) || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return false;
        }

        $absolutePath = $disk === 'public' && file_exists(public_path($path))
            ? public_path($path)
            : Storage::disk($disk)->path($path);

        return $this->optimizeFile($absolutePath, $maxDimension);
    }

    public function optimizeFile(string $absolutePath, int $maxDimension = 2200): bool
    {
        if (! extension_loaded('gd') || ! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return false;
        }

        $info = @getimagesize($absolutePath);
        if (! $info || ! in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            return false;
        }

        $markerDirectory = storage_path('framework/cache/image-optimization');
        $markerPath = $markerDirectory.'/'.sha1($absolutePath).'.sha1';
        $currentHash = sha1_file($absolutePath);
        if (is_file($markerPath) && trim((string) @file_get_contents($markerPath)) === $currentHash) {
            return false;
        }

        [$width, $height] = $info;
        $source = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($absolutePath),
            IMAGETYPE_PNG => @imagecreatefrompng($absolutePath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($absolutePath),
        };
        if (! $source) {
            return false;
        }

        $scale = min(1, $maxDimension / max($width, $height));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        if (in_array($info[2], [IMAGETYPE_PNG, IMAGETYPE_WEBP], true)) {
            imagealphablending($target, false);
            imagesavealpha($target, true);
            $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
            imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        $temporaryPath = $absolutePath.'.opt-'.bin2hex(random_bytes(4));
        $written = match ($info[2]) {
            IMAGETYPE_JPEG => imagejpeg($target, $temporaryPath, 82),
            IMAGETYPE_PNG => imagepng($target, $temporaryPath, 8),
            IMAGETYPE_WEBP => imagewebp($target, $temporaryPath, 82),
        };
        imagedestroy($source);
        imagedestroy($target);

        if (! $written || ! is_file($temporaryPath)) {
            @unlink($temporaryPath);
            return false;
        }

        $smaller = filesize($temporaryPath) < filesize($absolutePath);
        if (! $smaller) {
            @unlink($temporaryPath);
            $this->writeMarker($markerDirectory, $markerPath, $currentHash);
            return false;
        }

        $replaced = @copy($temporaryPath, $absolutePath);
        @unlink($temporaryPath);

        if ($replaced) {
            $this->writeMarker($markerDirectory, $markerPath, sha1_file($absolutePath));
        }

        return $replaced;
    }

    private function writeMarker(string $directory, string $path, string $hash): void
    {
        if (! is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }
        @file_put_contents($path, $hash, LOCK_EX);
    }
}
