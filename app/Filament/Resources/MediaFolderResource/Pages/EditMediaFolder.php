<?php

namespace App\Filament\Resources\MediaFolderResource\Pages;

use App\Filament\Resources\MediaFolderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMediaFolder extends EditRecord
{
    protected static string $resource = MediaFolderResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
