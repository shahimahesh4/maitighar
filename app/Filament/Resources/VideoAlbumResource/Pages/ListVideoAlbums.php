<?php
namespace App\Filament\Resources\VideoAlbumResource\Pages;
use App\Filament\Resources\VideoAlbumResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListVideoAlbums extends ListRecords { protected static string $resource = VideoAlbumResource::class; protected function getHeaderActions(): array { return [Actions\CreateAction::make()->label('Create video album')->icon('heroicon-o-plus')]; } }
