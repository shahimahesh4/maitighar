<?php

namespace App\Filament\Resources\MediaFolderResource\Pages;

use App\Filament\Resources\MediaFolderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMediaFolders extends ListRecords
{
    protected static string $resource = MediaFolderResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
