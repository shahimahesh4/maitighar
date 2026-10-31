<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use App\Services\ImageOptimizationService;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationGroup = 'Website';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Page Details')->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, $old, $operation, $get, $set): void {
                            if ($operation !== 'create' || blank($state)) {
                                return;
                            }

                            if (blank($get('slug')) || $get('slug') === Str::slug($old ?? '')) {
                                $set('slug', Str::slug($state));
                            }
                        }),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Auto-generated from title but editable.')
                        ->unique(Page::class, 'slug', ignoreRecord: true),

                    Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                        ])
                        ->default('draft')
                        ->required(),

                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                ])->columns(2),

                Section::make('Content')->schema([
                    Forms\Components\RichEditor::make('content')
                        ->label('Content')
                        ->columnSpanFull()
                        ->toolbarButtons([
                            'attachFiles',
                            'blockquote',
                            'bold',
                            'bulletList',
                            'codeBlock',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'orderedList',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                        ]),
                ]),

                Section::make('Gallery page header')
                    ->description('The page title and Header banner image control the public gallery header. Manage photo gallery albums and video albums in their dedicated Website menu items.')
                    ->visible(fn (Forms\Get $get): bool => in_array($get('slug'), ['gallery', 'videos'], true))
                    ->schema([
                        Forms\Components\Placeholder::make('gallery_header_note')
                            ->label('Dynamic gallery page')
                            ->content('Use the Page Title above for the public header title, and use Header banner image below for the full-width image. The page meta description is shown as the gallery introduction.'),
                    ]),

                Section::make('About Us information cards')
                    ->description('Add the Mission, Vision, Why Choose Us, or other information cards. Cards display three per row on desktop.')
                    ->visible(fn (Forms\Get $get): bool => $get('slug') === 'about-us')
                    ->schema([
                        Forms\Components\Repeater::make('about_info_boxes')
                            ->label('Information cards')
                            ->schema([
                                Select::make('icon')
                                    ->options([
                                        'bi-compass' => 'Compass — Mission',
                                        'bi-eye' => 'Eye — Vision',
                                        'bi-patch-check' => 'Badge check — Quality',
                                        'bi-heart' => 'Heart — Care',
                                        'bi-heart-fill' => 'Filled heart — Love',
                                        'bi-gem' => 'Gem — Craftsmanship',
                                        'bi-award' => 'Award — Excellence',
                                        'bi-trophy' => 'Trophy — Achievement',
                                        'bi-people' => 'People — Community',
                                        'bi-person-heart' => 'Person heart — Personal care',
                                        'bi-globe2' => 'Globe — Heritage',
                                        'bi-flower1' => 'Flower — Heritage',
                                        'bi-palette' => 'Palette — Creativity',
                                        'bi-brush' => 'Brush — Artistry',
                                        'bi-bag-heart' => 'Bag heart — Thoughtful products',
                                        'bi-box-seam' => 'Box — Products',
                                        'bi-mortarboard' => 'Graduation cap — Learning',
                                        'bi-book' => 'Book — Learning',
                                        'bi-lightbulb' => 'Light bulb — Inspiration',
                                        'bi-shield-check' => 'Shield check — Trust',
                                        'bi-hand-thumbs-up' => 'Thumbs up — Service',
                                        'bi-stars' => 'Stars — Special',
                                        'bi-sun' => 'Sun — Warmth',
                                        'bi-tree' => 'Tree — Sustainability',
                                        'bi-pin-map' => 'Map pin — Nepal',
                                        'bi-house-heart' => 'Home heart — Belonging',
                                    ])
                                    ->required()
                                    ->default('bi-patch-check'),
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(80),
                                Textarea::make('description')
                                    ->required()
                                    ->rows(4)
                                    ->maxLength(600)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Add information card')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New information card')
                            ->helperText('Use three cards for one full desktop row, or add more cards to create additional rows.'),
                    ]),

                Section::make('Media')->schema([
                    FileUpload::make('thumbnail_image')
                        ->label('Feature section image')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(2048)
                        ->disk('public')
                        ->directory('pages/thumbnails')
                        ->visibility('public')
                        ->imageResizeMode('contain')
                        ->imageResizeTargetWidth('1200')
                        ->imageResizeTargetHeight('1200')
                        ->imageResizeUpscale(false)
                        ->saveUploadedFileUsing(static fn (TemporaryUploadedFile $file): ?string => app(ImageOptimizationService::class)
                            ->storeAsWebp($file->getRealPath(), 'public', 'pages/thumbnails', 1200, 1200))
                        ->deleteUploadedFileUsing(static fn (string $file): bool => Storage::disk('public')->delete($file))
                        ->helperText('Used in the feature section within the page content. JPG, PNG, or WebP up to 2 MB; saved as optimized WebP (maximum 1200×1200px).'),

                    FileUpload::make('banner_image')
                        ->label('Header banner image')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(2048)
                        ->disk('public')
                        ->directory('pages/banners')
                        ->visibility('public')
                        ->imageResizeMode('contain')
                        ->imageResizeTargetWidth('1920')
                        ->imageResizeTargetHeight('900')
                        ->imageResizeUpscale(false)
                        ->saveUploadedFileUsing(static fn (TemporaryUploadedFile $file): ?string => app(ImageOptimizationService::class)
                            ->storeAsWebp($file->getRealPath(), 'public', 'pages/banners', 1920, 900))
                        ->deleteUploadedFileUsing(static fn (string $file): bool => Storage::disk('public')->delete($file))
                        ->helperText('Used in the full-width page header. JPG, PNG, or WebP up to 2 MB; saved as optimized WebP (maximum 1920×900px).'),
                ])->columns(2),

                Section::make('SEO')->schema([
                    TextInput::make('meta_title')
                        ->maxLength(255),
                    Textarea::make('meta_description')
                        ->rows(3),
                    Textarea::make('meta_keywords')
                        ->rows(2),
                ])->columns(2),

                Section::make('FAQs')->schema([
                    Forms\Components\Repeater::make('faqs')
                        ->relationship()
                        ->schema([
                            TextInput::make('question')->required()->maxLength(255),
                            Forms\Components\RichEditor::make('answer')->required()->columnSpanFull(),
                            Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                ])
                                ->default('published')
                                ->required(),
                            TextInput::make('sort_order')->numeric()->default(0),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'New FAQ'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
