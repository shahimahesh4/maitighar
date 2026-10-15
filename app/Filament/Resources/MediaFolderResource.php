<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaFolderResource\Pages;
use App\Models\MediaFolder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MediaFolderResource extends Resource
{
    protected static ?string $model = MediaFolder::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'Media';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Folder details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('parent_id')
                        ->label('Parent folder')
                        ->options(fn (?MediaFolder $record): array => MediaFolder::query()
                            ->when($record, fn (Builder $query) => $query->whereKeyNot($record->getKey()))
                            ->get()
                            ->mapWithKeys(fn (MediaFolder $folder) => [$folder->id => $folder->full_name])
                            ->all())
                        ->searchable()
                        ->preload()
                        ->placeholder('Top level'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->icon('heroicon-o-folder')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.full_name')
                    ->label('Parent')
                    ->placeholder('Top level'),
                Tables\Columns\TextColumn::make('assets_count')
                    ->counts('assets')
                    ->label('Files')
                    ->badge(),
                Tables\Columns\TextColumn::make('children_count')
                    ->counts('children')
                    ->label('Folders')
                    ->badge(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaFolders::route('/'),
            'create' => Pages\CreateMediaFolder::route('/create'),
            'edit' => Pages\EditMediaFolder::route('/{record}/edit'),
        ];
    }
}
