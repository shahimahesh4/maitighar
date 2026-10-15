<?php

namespace App\Filament\Resources\MediaAssetResource\Pages;

use App\Filament\Resources\MediaAssetResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateMediaAsset extends CreateRecord
{
    protected static string $resource = MediaAssetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['disk'] = $data['disk'] ?? 'public';
        $data['name'] = filled($data['name'] ?? null) ? $data['name'] : basename($data['path']);
        $data['mime_type'] = Storage::disk($data['disk'])->mimeType($data['path']) ?: null;
        $data['size'] = Storage::disk($data['disk'])->size($data['path']);

        return $data;
    }
}
