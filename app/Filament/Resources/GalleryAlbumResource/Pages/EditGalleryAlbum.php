<?php

namespace App\Filament\Resources\GalleryAlbumResource\Pages;

use App\Filament\Resources\GalleryAlbumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGalleryAlbum extends EditRecord
{
    protected static string $resource = GalleryAlbumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view')->label('View album')->icon('heroicon-o-arrow-top-right-on-square')->url(route('gallery.show', $this->record))->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }
}
