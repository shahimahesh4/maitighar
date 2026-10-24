<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Brand;
use App\Models\Category;
use App\Models\MediaAsset;
use App\Models\MediaFolder;
use App\Models\Product;
use App\Services\Ai\GeminiService;
use App\Support\Money;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Product Information')->schema([

                        Select::make('product_type')
                            ->label('Product Type')
                            ->options([
                                'simple' => 'Simple Product',
                                'variable' => 'Variable Product',
                            ])
                            ->default('simple')
                            ->required()
                            ->live()
                            ->helperText('Choose "Variable Product" to manage attributes (Size, Colour, etc.) and variations in the tabs below.')
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Product Name')
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
                            })
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generateAIDescription')
                                    ->label('Generate AI Description')
                                    ->icon('heroicon-o-sparkles')
                                    ->color('primary')
                                    ->action(function (Forms\Get $get, Forms\Set $set, $state) {
                                        if (empty($state)) {
                                            Notification::make()
                                                ->warning()
                                                ->title('Product Name Required')
                                                ->body('Please type a product name first before generating.')
                                                ->send();

                                            return;
                                        }

                                        $categoryId = $get('category_id');
                                        $brandId = $get('brand_id');

                                        $categoryName = $categoryId ? Category::find($categoryId)?->name : 'Products';
                                        $brandName = $brandId ? Brand::find($brandId)?->name : null;

                                        Notification::make()
                                            ->info()
                                            ->title('AI Copywriter Active')
                                            ->body('Writing description with Gemini API...')
                                            ->send();

                                        $service = app(GeminiService::class);
                                        $description = $service->generateProductDescription($state, $categoryName, $brandName);

                                        if ($description) {
                                            $set('description', $description);
                                            Notification::make()
                                                ->success()
                                                ->title('Description Generated!')
                                                ->send();
                                        } else {
                                            Notification::make()
                                                ->danger()
                                                ->title('AI Copywriter Failed')
                                                ->body('Verify your Gemini API key in Store Settings.')
                                                ->send();
                                        }
                                    })
                            ),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Auto-generated from the product name, but you can edit it.')
                            ->unique(Product::class, 'slug', ignoreRecord: true),

                        RichEditor::make('description')
                            ->columnSpanFull()
                            ->label('Long Description')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('products'),

                        RichEditor::make('short_description')
                            ->columnSpanFull()
                            ->label('Short Description')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('products'),

                        TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->maxLength(255)
                            ->default(fn () => Product::generateNextSku())
                            ->unique(Product::class, 'sku', ignoreRecord: true)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generateSku')
                                    ->label('Generate Next SKU')
                                    ->icon('heroicon-m-arrow-path')
                                    ->tooltip('Generate next available sequential SKU')
                                    ->action(function (Forms\Set $set) {
                                        $next = Product::generateNextSku();
                                        $set('sku', $next);
                                        Notification::make()
                                            ->success()
                                            ->title('SKU Generated')
                                            ->body("Assigned next sequential SKU: {$next}")
                                            ->send();
                                    })
                            )
                            ->helperText('Auto-generated sequential SKU format (e.g. MG-AUS-001). Can be edited manually if needed.')
                            ->columnSpanFull(),

                    ])->columns(2),

                    Section::make('Images')->schema([
                        FileUpload::make('main_image')
                            ->label('Main Product Image')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->directory('products/main')
                            ->maxSize(5120)
                            ->openable()
                            ->downloadable()
                            ->helperText('Used on product cards, listings, and as the large primary image on the product page.'),

                        FileUpload::make('gallery_images')
                            ->label('Product Gallery Images')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->multiple()
                            ->directory('products/gallery')
                            ->maxFiles(10)
                            ->maxSize(5120)
                            ->openable()
                            ->downloadable()
                            ->reorderable()
                            ->helperText('Upload multiple images for the scrolling thumbnail gallery on the product details page.'),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('openMediaGallery')
                                ->label('Choose from Media Gallery')
                                ->icon('heroicon-o-photo')
                                ->color('primary')
                                ->modalHeading('Product Media Gallery')
                                ->modalDescription('Choose existing media or upload new files without leaving the product form.')
                                ->modalWidth('6xl')
                                ->form([
                                    Forms\Components\Radio::make('media_target')
                                        ->label('Add selected media to')
                                        ->options([
                                            'main' => 'Main Product Image',
                                            'gallery' => 'Scrolling Gallery',
                                        ])
                                        ->default('gallery')
                                        ->inline()
                                        ->required(),
                                    Forms\Components\Tabs::make('media_picker_tabs')
                                        ->tabs([
                                            Forms\Components\Tabs\Tab::make('Existing Media')
                                                ->icon('heroicon-o-photo')
                                                ->schema([
                                                    Select::make('existing_media')
                                                        ->label('Select files from the media library')
                                                        ->multiple()
                                                        ->searchable()
                                                        ->preload()
                                                        ->allowHtml()
                                                        ->options(fn (): array => MediaAsset::query()
                                                            ->with('folder')
                                                            ->where('mime_type', 'like', 'image/%')
                                                            ->latest()
                                                            ->get()
                                                            ->mapWithKeys(fn (MediaAsset $asset): array => [
                                                                $asset->id => '<div style="display:flex;align-items:center;gap:10px"><img src="'.e($asset->url).'" alt="" style="width:42px;height:42px;object-fit:cover;border-radius:6px"><span>'.e($asset->name).'<br><small>'.e($asset->folder?->full_name ?? 'Unfiled').'</small></span></div>',
                                                            ])
                                                            ->all())
                                                        ->helperText('Search by file name and select one or more existing images.'),
                                                ]),
                                            Forms\Components\Tabs\Tab::make('Upload New')
                                                ->icon('heroicon-o-arrow-up-tray')
                                                ->schema([
                                                    Select::make('media_folder_id')
                                                        ->label('Folder')
                                                        ->options(fn (): array => MediaFolder::query()->get()->pluck('full_name', 'id')->all())
                                                        ->searchable()
                                                        ->preload()
                                                        ->placeholder('Unfiled'),
                                                    FileUpload::make('new_media')
                                                        ->label('Upload media')
                                                        ->disk('public')
                                                        ->directory('media')
                                                        ->visibility('public')
                                                        ->image()
                                                        ->imageEditor()
                                                        ->multiple()
                                                        ->maxFiles(5)
                                                        ->maxSize(5120)
                                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                                        ->columnSpanFull(),
                                                    TextInput::make('media_alt_text')
                                                        ->label('Alternative text')
                                                        ->maxLength(255),
                                                    Textarea::make('media_caption')
                                                        ->label('Caption')
                                                        ->rows(3),
                                                ])
                                                ->columns(2),
                                        ])
                                        ->columnSpanFull(),
                                ])
                                ->action(function (array $data, Forms\Get $get, Forms\Set $set): void {
                                    $selectedPaths = MediaAsset::query()
                                        ->whereIn('id', $data['existing_media'] ?? [])
                                        ->pluck('path')
                                        ->all();

                                    foreach (($data['new_media'] ?? []) as $path) {
                                        $asset = MediaAsset::firstOrCreate(
                                            ['disk' => 'public', 'path' => $path],
                                            [
                                                'media_folder_id' => $data['media_folder_id'] ?? null,
                                                'name' => basename($path),
                                                'mime_type' => Storage::disk('public')->mimeType($path) ?: null,
                                                'size' => Storage::disk('public')->size($path),
                                                'alt_text' => $data['media_alt_text'] ?? null,
                                                'caption' => $data['media_caption'] ?? null,
                                            ],
                                        );
                                        $selectedPaths[] = $asset->path;
                                    }

                                    $selectedPaths = array_values(array_unique(array_filter($selectedPaths)));
                                    if (($data['media_target'] ?? 'gallery') === 'main' && $selectedPaths !== []) {
                                        $set('main_image', array_shift($selectedPaths));
                                    }

                                    if ($selectedPaths !== []) {
                                        $gallery = array_values(array_filter($get('gallery_images') ?? []));
                                        $set('gallery_images', array_slice(array_values(array_unique(array_merge($gallery, $selectedPaths))), 0, 10));
                                    }

                                    Notification::make()
                                        ->success()
                                        ->title('Product images updated')
                                        ->body('Selected media has been assigned to the requested image area. Save the product to keep the changes.')
                                        ->send();
                                }),
                        ])->fullWidth(),
                    ]),

                    Section::make('Dynamic Product Details')->schema([
                        Forms\Components\Repeater::make('specifications')
                            ->label('Specifications')
                            ->schema([
                                TextInput::make('label')->required()->placeholder('e.g. Brand'),
                                TextInput::make('value')->required()->placeholder('e.g. Maitighar'),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add specification')
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('product_tabs')
                            ->label('Product Detail Tabs')
                            ->helperText('Add as many tabs as needed. Each tab appears automatically on the product details page.')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Tab Title')
                                    ->required()
                                    ->maxLength(80)
                                    ->placeholder('e.g. Delivery Policy'),
                                RichEditor::make('content')
                                    ->label('Tab Description')
                                    ->required()
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('products/tabs')
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'strike',
                                        'link',
                                        'bulletList',
                                        'orderedList',
                                        'h2',
                                        'h3',
                                        'blockquote',
                                        'undo',
                                        'redo',
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New tab')
                            ->addActionLabel('Add another tab')
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->columnSpanFull(),
                    ])->collapsible(),
                    Section::make('SEO Data')->schema([

                        TextInput::make('meta_title')
                            ->maxLength(255)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('generateAISeo')
                                    ->label('Generate AI SEO')
                                    ->icon('heroicon-o-sparkles')
                                    ->color('primary')
                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                        $name = $get('name');
                                        $desc = $get('description') ?: $get('short_description') ?: '';

                                        if (empty($name)) {
                                            Notification::make()
                                                ->warning()
                                                ->title('Product Name Required')
                                                ->body('Please type a product name first.')
                                                ->send();

                                            return;
                                        }

                                        Notification::make()
                                            ->info()
                                            ->title('SEO Optimizer Active')
                                            ->body('Writing tags with Gemini API...')
                                            ->send();

                                        $service = app(GeminiService::class);
                                        $seo = $service->generateSeoTags($name, strip_tags($desc));

                                        $set('meta_title', $seo['meta_title']);
                                        $set('meta_description', $seo['meta_description']);

                                        Notification::make()
                                            ->success()
                                            ->title('SEO Tags Generated & Applied!')
                                            ->send();
                                    })
                            ),

                        Textarea::make('meta_description')
                            ->autosize(),

                        TextInput::make('meta_keywords')
                            ->maxLength(255),

                    ]),
                ])->columnSpan(2),

                Group::make()->schema([

                    Section::make('Price (AUD)')
                        ->description(fn (Forms\Get $get) => ($get('product_type') ?? 'simple') === 'variable'
                            ? 'For variable products, prices are managed per-variation in the Variations tab.'
                            : null)
                        ->schema([
                            TextInput::make('sale_price')
                                ->label('Price (AUD)')
                                ->numeric()
                                ->required(fn (Forms\Get $get) => ($get('product_type') ?? 'simple') === 'simple')
                                ->prefix('$')
                                ->helperText(fn (Forms\Get $get) => ($get('product_type') ?? 'simple') === 'variable'
                                    ? 'Base / minimum price across variations.'
                                    : null),
                            TextInput::make('regular_price')
                                ->label('Regular Price (AUD)')
                                ->numeric()
                                ->prefix('$')
                                ->gte('sale_price')
                                ->helperText('Discount percentage is calculated automatically from regular and sale prices.'),
                        ]),

                    Section::make('Associations')->schema([

                        Select::make('category_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('category', 'name')
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('suggestCategory')
                                    ->label('AI Suggest Category')
                                    ->icon('heroicon-o-sparkles')
                                    ->color('primary')
                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                        $name = $get('name');
                                        $desc = $get('description') ?: $get('short_description') ?: '';

                                        if (empty($name)) {
                                            Notification::make()
                                                ->warning()
                                                ->title('Product Name Required')
                                                ->body('Please type a product name first.')
                                                ->send();

                                            return;
                                        }

                                        $categoryId = $get('category_id');
                                        $brandId = $get('brand_id');

                                        $categoryName = $categoryId ? Category::find($categoryId)?->name : 'Products';
                                        $brandName = $brandId ? Brand::find($brandId)?->name : null;

                                        Notification::make()
                                            ->info()
                                            ->title('AI Categorizer Active')
                                            ->body('Analyzing category with Gemini API...')
                                            ->send();

                                        $service = app(GeminiService::class);
                                        $categoryName = $service->suggestCategory($name, strip_tags($desc));

                                        if ($categoryName) {
                                            $category = Category::where('name', 'like', "%{$categoryName}%")->first();
                                            if ($category) {
                                                $set('category_id', $category->id);
                                                Notification::make()
                                                    ->success()
                                                    ->title("Categorized: {$category->name}")
                                                    ->send();
                                            } else {
                                                Notification::make()
                                                    ->warning()
                                                    ->title('Category Not Mapped')
                                                    ->body("AI suggested '{$categoryName}', but category was not found in database.")
                                                    ->send();
                                            }
                                        } else {
                                            Notification::make()
                                                ->danger()
                                                ->title('AI Categorizer Failed')
                                                ->body('Verify your Gemini API key in Store Settings.')
                                                ->send();
                                        }
                                    })
                            ),

                        Select::make('brand_id')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('brand', 'name'),
                    ]),

                    Section::make('Inventory & Status')->schema([

                        TextInput::make('stock_quantity')
                            ->label('Stock Quantity')
                            ->numeric()
                            ->default(0)
                            ->visible(fn (Forms\Get $get) => ($get('product_type') ?? 'simple') === 'simple')
                            ->helperText('Stock quantity for simple product. Variable products track stock per variation.'),

                        Toggle::make('in_stock')
                            ->required()
                            ->default(true),

                        Toggle::make('is_active')
                            ->required()
                            ->default(true),

                        Toggle::make('is_featured')
                            ->required(),

                        Toggle::make('on_sale')
                            ->required(),

                    ]),

                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\ImageColumn::make('images')
                    ->label('Image')
                    ->getStateUsing(fn (Product $record): string => $record->getImageUrl())
                    ->square(),

                Tables\Columns\TextColumn::make('product_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'variable' ? 'Variable' : 'Simple')
                    ->color(fn ($state) => $state === 'variable' ? 'warning' : 'info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sale_price')
                    ->label('Price (AUD)')
                    ->getStateUsing(fn (Product $record) => $record->getFormattedPriceRange())
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderBy('sale_price', $direction);
                    }),

                Tables\Columns\ToggleColumn::make('is_active'),

                Tables\Columns\ToggleColumn::make('is_featured'),

                Tables\Columns\ToggleColumn::make('in_stock'),

                Tables\Columns\ToggleColumn::make('on_sale'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('product_type')
                    ->options([
                        'simple' => 'Simple Product',
                        'variable' => 'Variable Product',
                    ]),

                SelectFilter::make('category')
                    ->relationship('category', 'name'),

                SelectFilter::make('brand')
                    ->relationship('brand', 'name'),

                Filter::make('is_featured')
                    ->toggle(),

                Filter::make('in_stock')
                    ->toggle(),

                Filter::make('on_sale')
                    ->toggle(),

                Filter::make('is_active')
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->requiresConfirmation(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultPaginationPageOption(12)
            ->paginationPageOptions([12, 25, 50, 100])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\AttributesRelationManager::class,
            RelationManagers\VariationsRelationManager::class,
            RelationManagers\FaqsRelationManager::class,
            RelationManagers\InventoryLogsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['category', 'brand']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
