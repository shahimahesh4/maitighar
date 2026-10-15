<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaAssetResource\Pages;
use App\Models\MediaAsset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class MediaAssetResource extends Resource
{
    protected static ?string $model = MediaAsset::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Media';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Media Library';

    protected static ?string $modelLabel = 'media file';

    protected static ?string $pluralModelLabel = 'media library';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('File')
                ->schema([
                    Forms\Components\FileUpload::make('path')
                        ->label('Upload media')
                        ->disk('public')
                        ->directory('media')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->downloadable()
                        ->openable()
                        ->maxSize(20480)
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->columnSpanFull(),
                    Forms\Components\Select::make('media_folder_id')
                        ->label('Folder')
                        ->relationship('folder', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Unfiled'),
                    Forms\Components\TextInput::make('name')
                        ->label('Display name')
                        ->maxLength(255)
                        ->helperText('Defaults to the uploaded filename.'),
                    Forms\Components\Hidden::make('disk')->default('public'),
                ])
                ->columns(2),
            Forms\Components\Section::make('Accessibility and details')
                ->schema([
                    Forms\Components\TextInput::make('alt_text')
                        ->label('Alternative text')
                        ->maxLength(255)
                        ->helperText('Describe the image for screen readers and when it cannot load.'),
                    Forms\Components\Textarea::make('caption')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('path')
                    ->label('Preview')
                    ->disk(fn (MediaAsset $record): string => $record->disk)
                    ->square()
                    ->size(64),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (MediaAsset $record): ?string => $record->alt_text),
                Tables\Columns\TextColumn::make('folder.full_name')
                    ->label('Folder')
                    ->placeholder('Unfiled')
                    ->badge(),
                Tables\Columns\TextColumn::make('mime_type')
                    ->label('Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->label('Size')
                    ->formatStateUsing(fn (?int $state): string => $state
                        ? number_format($state / 1024, $state < 1048576 ? 0 : 1).' '.($state < 1048576 ? 'KB' : 'MB')
                        : '—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('media_folder_id')
                    ->label('Folder')
                    ->relationship('folder', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (MediaAsset $record): string => Storage::disk($record->disk)->url($record->path))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMediaAssets::route('/'),
            'create' => Pages\CreateMediaAsset::route('/create'),
            'edit' => Pages\EditMediaAsset::route('/{record}/edit'),
        ];
    }
}
