<?php

namespace App\Filament\Resources\MediaAssetResource\Pages;

use App\Filament\Resources\MediaAssetResource;
use App\Models\MediaAsset;
use App\Models\MediaFolder;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListMediaAssets extends ListRecords
{
    protected static string $resource = MediaAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('scanStorage')
                ->label('Scan existing files')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Indexes existing files in the public media and product folders. Payment vouchers are never included.')
                ->action(function (): void {
                    $count = $this->scanStorage();

                    Notification::make()
                        ->success()
                        ->title('Media library synchronized')
                        ->body("{$count} new file(s) were added to the media library.")
                        ->send();
                }),
            Actions\CreateAction::make()->label('Upload media'),
        ];
    }

    protected function scanStorage(): int
    {
        $disk = Storage::disk('public');
        $paths = array_unique(array_merge(
            $disk->allFiles('media'),
            $disk->allFiles('products'),
        ));
        $created = 0;

        foreach ($paths as $path) {
            $directory = trim(str_replace('\\', '/', dirname($path)), './');
            $folderId = $this->findOrCreateFolder($directory);

            $asset = MediaAsset::firstOrCreate(
                ['disk' => 'public', 'path' => $path],
                [
                    'media_folder_id' => $folderId,
                    'name' => basename($path),
                    'mime_type' => $disk->mimeType($path) ?: null,
                    'size' => $disk->size($path),
                ],
            );

            $created += $asset->wasRecentlyCreated ? 1 : 0;
        }

        return $created;
    }

    protected function findOrCreateFolder(string $directory): ?int
    {
        if ($directory === '') {
            return null;
        }

        $parentId = null;
        foreach (array_filter(explode('/', $directory)) as $segment) {
            $folder = MediaFolder::firstOrCreate(
                ['parent_id' => $parentId, 'name' => Str::headline($segment)],
            );
            $parentId = $folder->id;
        }

        return $parentId;
    }
}
