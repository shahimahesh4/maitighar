<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoAlbumResource\Pages;
use App\Models\VideoAlbum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class VideoAlbumResource extends Resource
{
    protected static ?string $model = VideoAlbum::class;
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationLabel = 'Video Gallery';
    protected static ?string $modelLabel = 'video album';
    protected static ?string $navigationGroup = 'Website';
    protected static ?int $navigationSort = 8;
    protected static ?string $recordTitleAttribute = 'title';

    public static function canAccess(): bool { return auth()->user()?->hasAdminPermission('content') ?? false; }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Video album manager')->tabs([
                Forms\Components\Tabs\Tab::make('Album details')->icon('heroicon-o-pencil-square')->schema([
                    Forms\Components\Section::make('Album information')->schema([
                        Forms\Components\TextInput::make('title')->required()->maxLength(160)->live(onBlur: true)->afterStateUpdated(function (?string $state, $old, string $operation, $get, $set): void { if ($operation === 'create' && filled($state) && (blank($get('slug')) || $get('slug') === Str::slug($old ?? ''))) $set('slug', Str::slug($state)); }),
                        Forms\Components\TextInput::make('slug')->required()->maxLength(180)->helperText('Auto-generated from the album title, but you can edit it.')->unique(VideoAlbum::class, 'slug', ignoreRecord: true),
                        Forms\Components\Textarea::make('description')->rows(4)->maxLength(600)->columnSpanFull(),
                        Forms\Components\FileUpload::make('cover_image')->label('Album cover')->image()->disk('public')->directory('videos/covers')->visibility('public')->imageEditor()->imageEditorAspectRatios(['16:9'])->maxSize(8192)->columnSpanFull(),
                    ])->columns(2),
                ]),
                Forms\Components\Tabs\Tab::make('Videos')->icon('heroicon-o-video-camera')->badge(fn (?VideoAlbum $record) => $record?->video_count ?? 0)->schema([
                    Forms\Components\Section::make('Video upload options')->description('Add YouTube videos by link or upload MP4, WebM, and MOV files directly. Drag items to reorder the public gallery.')->schema([
                        Forms\Components\Repeater::make('videos')->label('Videos')->reorderable()->collapsible()->cloneable()->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New video')->schema([
                            Forms\Components\TextInput::make('title')->required()->maxLength(180),
                            Forms\Components\Select::make('type')->label('Video source')->options(['youtube' => 'Manual YouTube link', 'upload' => 'Upload video file'])->default('youtube')->required()->live()->helperText('Choose whether this video comes from YouTube or is uploaded to the website.'),
                            Forms\Components\TextInput::make('youtube_url')->label('Manual YouTube video link')->url()->placeholder('https://www.youtube.com/watch?v=...')->helperText('Paste a YouTube watch, short, or share URL. This video will appear automatically in the public gallery.')->visible(fn (Forms\Get $get): bool => $get('type') !== 'upload')->required(fn (Forms\Get $get): bool => $get('type') !== 'upload')->columnSpanFull(),
                            Forms\Components\FileUpload::make('video_file')->label('Upload video file')->disk('public')->directory('videos/files')->visibility('public')->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])->maxSize(204800)->visible(fn (Forms\Get $get): bool => $get('type') === 'upload')->required(fn (Forms\Get $get): bool => $get('type') === 'upload')->downloadable()->openable()->helperText('Accepted: MP4, WebM, or MOV. Maximum file size: 200 MB.')->columnSpanFull(),
                            Forms\Components\FileUpload::make('thumbnail')->label('Custom thumbnail')->image()->disk('public')->directory('videos/thumbnails')->visibility('public')->imageEditor()->imageEditorAspectRatios(['16:9'])->maxSize(8192)->helperText('Optional for YouTube. Recommended for uploaded videos.')->columnSpanFull(),
                        ])->columns(2)->defaultItems(1)->addActionLabel('Add video')->columnSpanFull(),
                    ]),
                ]),
                Forms\Components\Tabs\Tab::make('Publishing')->icon('heroicon-o-globe-alt')->schema([
                    Forms\Components\Section::make('Visibility and order')->schema([
                        Forms\Components\Toggle::make('is_active')->label('Published')->default(true),
                        Forms\Components\TextInput::make('sort_order')->numeric()->minValue(0)->default(0),
                        Forms\Components\DateTimePicker::make('published_at')->seconds(false),
                    ])->columns(3),
                ]),
            ])->persistTabInQueryString()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('cover_image')->label('Cover')->disk('public')->square()->defaultImageUrl(asset('images/kba/about-us-academy.webp')),
            Tables\Columns\TextColumn::make('title')->searchable()->sortable()->description(fn (VideoAlbum $record) => $record->slug),
            Tables\Columns\TextColumn::make('video_count')->label('Videos')->badge()->color('warning'),
            Tables\Columns\TextColumn::make('sort_order')->label('Order')->sortable(),
            Tables\Columns\ToggleColumn::make('is_active')->label('Published'),
            Tables\Columns\TextColumn::make('updated_at')->since()->sortable(),
        ])->actions([
            Tables\Actions\Action::make('view')->icon('heroicon-o-arrow-top-right-on-square')->url(fn (VideoAlbum $record) => route('videos.show', $record))->openUrlInNewTab(),
            Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])->reorderable('sort_order')->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListVideoAlbums::route('/'), 'create' => Pages\CreateVideoAlbum::route('/create'), 'edit' => Pages\EditVideoAlbum::route('/{record}/edit')];
    }
}
