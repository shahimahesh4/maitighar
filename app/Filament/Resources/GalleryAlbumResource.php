<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryAlbumResource\Pages;
use App\Models\GalleryAlbum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class GalleryAlbumResource extends Resource
{
    protected static ?string $model = GalleryAlbum::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Photo Gallery';
    protected static ?string $modelLabel = 'Gallery album';
    protected static ?string $pluralModelLabel = 'Gallery albums';
    protected static ?string $navigationGroup = 'Website';
    protected static ?int $navigationSort = 7;
    protected static ?string $recordTitleAttribute = 'title';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAdminPermission('content') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Album manager')->tabs([
                Tabs\Tab::make('Album details')->icon('heroicon-o-pencil-square')->schema([
                    Section::make('Album information')
                        ->description('Give this collection a clear title and short introduction for visitors.')
                        ->schema([
                            TextInput::make('title')->required()->maxLength(160)->live(onBlur: true)
                                ->afterStateUpdated(function (?string $state, $old, string $operation, $get, $set): void {
                                    if ($operation === 'create' && filled($state) && (blank($get('slug')) || $get('slug') === Str::slug($old ?? ''))) {
                                        $set('slug', Str::slug($state));
                                    }
                                }),
                            TextInput::make('slug')->required()->maxLength(180)
                                ->unique(GalleryAlbum::class, 'slug', ignoreRecord: true)
                                ->helperText('Auto-generated from the album title, but you can edit it.'),
                            Textarea::make('description')->rows(4)->maxLength(600)->columnSpanFull()
                                ->helperText('A short introduction shown on the album card and album page.'),
                        ])->columns(2),
                    Section::make('Cover image')
                        ->description('Upload one strong landscape image that represents the whole album.')
                        ->schema([
                            FileUpload::make('cover_image')->label('Album thumbnail')->image()->disk('public')
                                ->directory('gallery/covers')->visibility('public')->imageEditor()
                                ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
                                ->maxSize(8192)->helperText('Recommended: 1600 × 1000 px. Automatically resized and compressed after upload.'),
                        ]),
                ]),
                Tabs\Tab::make('Album photos')->icon('heroicon-o-photo')->badge(fn (?GalleryAlbum $record) => $record?->image_count ?? 0)->schema([
                    Section::make('Bulk photo uploader')
                        ->description('Select many images at once, then drag them into the order you want visitors to see.')
                        ->schema([
                            FileUpload::make('images')->label('Album images')->multiple()->image()->disk('public')
                                ->directory('gallery/albums')->visibility('public')->appendFiles()->reorderable()
                                ->panelLayout('grid')->imagePreviewHeight('150')->maxFiles(300)->maxSize(12288)
                                ->helperText('Upload up to 300 JPG, PNG, or WebP images. Each image is automatically resized to a maximum of 1920 px and compressed.'),
                        ]),
                ]),
                Tabs\Tab::make('Publishing')->icon('heroicon-o-globe-alt')->schema([
                    Section::make('Visibility and order')->schema([
                        Toggle::make('is_active')->label('Published')->default(true)
                            ->helperText('Turn off to hide this album from the public gallery.'),
                        TextInput::make('sort_order')->numeric()->minValue(0)->default(0)
                            ->helperText('Lower numbers appear first.'),
                        \Filament\Forms\Components\DateTimePicker::make('published_at')->seconds(false)
                            ->helperText('Optional. Schedule the album to appear at a future date.'),
                    ])->columns(3),
                ]),
            ])->persistTabInQueryString()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('cover_image')->label('Cover')->disk('public')->square()->defaultImageUrl(asset('images/kba/about-us-academy.png')),
            Tables\Columns\TextColumn::make('title')->searchable()->sortable()->description(fn (GalleryAlbum $record) => $record->slug),
            Tables\Columns\TextColumn::make('image_count')->label('Photos')->badge()->color('warning'),
            Tables\Columns\TextColumn::make('sort_order')->label('Order')->sortable(),
            Tables\Columns\ToggleColumn::make('is_active')->label('Published'),
            Tables\Columns\TextColumn::make('updated_at')->since()->label('Updated')->sortable(),
        ])->actions([
            Tables\Actions\Action::make('view')->label('View')->icon('heroicon-o-arrow-top-right-on-square')->url(fn (GalleryAlbum $record) => route('gallery.show', $record))->openUrlInNewTab(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
        ])->reorderable('sort_order')->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleryAlbums::route('/'),
            'create' => Pages\CreateGalleryAlbum::route('/create'),
            'edit' => Pages\EditGalleryAlbum::route('/{record}/edit'),
        ];
    }
}
