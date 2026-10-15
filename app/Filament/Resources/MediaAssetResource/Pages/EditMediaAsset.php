<?php

namespace App\Filament\Resources\MediaAssetResource\Pages;

use App\Filament\Resources\MediaAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditMediaAsset extends EditRecord
{
    protected static string $resource = MediaAssetResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['path'] ?? null) !== $this->record->path) {
            $disk = $data['disk'] ?? 'public';
            $data['mime_type'] = Storage::disk($disk)->mimeType($data['path']) ?: null;
            $data['size'] = Storage::disk($disk)->size($data['path']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $oldPath = $this->record->getOriginal('path');

        if ($oldPath && $oldPath !== $this->record->path) {
            Storage::disk($this->record->disk)->delete($oldPath);
        }
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
