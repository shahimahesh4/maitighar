<?php
namespace App\Filament\Resources\VideoAlbumResource\Pages;
use App\Filament\Resources\VideoAlbumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditVideoAlbum extends EditRecord { protected static string $resource = VideoAlbumResource::class; protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; } }
