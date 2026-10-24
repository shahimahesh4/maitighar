<?php

namespace App\Filament\Resources\SliderResource\RelationManagers;

use App\Models\SliderItem;
use App\Services\ImageOptimizationService;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SliderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $title = 'Slides';

    public function form(Form $form): Form
    {
        return $form->schema([
            // Top Section: Core Details & Type Selection
            Section::make('Slide Configuration')
                ->description('Choose whether this slide is a simple full-image banner or an interactive Revolution-style animated layered banner.')
                ->schema([
                    TextInput::make('title')
                        ->label('Slide Title / Name')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Used to identify this slide in the admin panel and for image alt attributes.'),

                    Select::make('type')
                        ->label('Slider Type')
                        ->options([
                            'animated_content' => 'Animated Content Banner (Revolution Slider style)',
                            'single_image' => 'Single Image Banner (Full-bleed graphic)',
                        ])
                        ->default('animated_content')
                        ->required()
                        ->live()
                        ->helperText('Single Image Banner displays an uploaded banner image. Animated Content Banner enables interactive text, buttons, floating product graphics, and animations.'),

                    Toggle::make('is_active')
                        ->label('Published / Active')
                        ->default(true)
                        ->inline(false),
                ])->columns(3),

            // ==========================================
            // OPTION 1: SINGLE IMAGE BANNER FORM
            // ==========================================
            Section::make('Single Image Banner')
                ->description('Upload full banner graphics. Text, slogans, buttons, and animations are hidden for this mode.')
                ->visible(fn (Get $get) => $get('type') === 'single_image')
                ->schema([
                    FileUpload::make('desktop_image')
                        ->label('Desktop Banner Image')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->disk('public')
                        ->directory('sliders')
                        ->visibility('public')
                        ->imagePreviewHeight('220')
                        ->imageResizeMode('contain')
                        ->imageResizeTargetWidth('1920')
                        ->imageResizeTargetHeight('800')
                        ->imageResizeUpscale(false)
                        ->saveUploadedFileUsing(static fn (TemporaryUploadedFile $file): ?string => app(ImageOptimizationService::class)
                            ->storeAsWebp($file->getRealPath(), 'public', 'sliders', 1920, 800))
                        ->deletable()
                        ->removeUploadedFileButtonPosition('right')
                        ->deleteUploadedFileUsing(static fn (string $file): bool => Storage::disk('public')->delete($file))
                        ->openable()
                        ->downloadable()
                        ->required(fn (Get $get) => $get('type') === 'single_image')
                        ->helperText('Saved as an optimized WebP image, up to 1920×800px without cropping.'),

                    FileUpload::make('mobile_image')
                        ->label('Mobile Banner Image (Optional)')
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->disk('public')
                        ->directory('sliders/mobile')
                        ->visibility('public')
                        ->imagePreviewHeight('220')
                        ->imageResizeMode('contain')
                        ->imageResizeTargetWidth('768')
                        ->imageResizeTargetHeight('480')
                        ->imageResizeUpscale(false)
                        ->saveUploadedFileUsing(static fn (TemporaryUploadedFile $file): ?string => app(ImageOptimizationService::class)
                            ->storeAsWebp($file->getRealPath(), 'public', 'sliders/mobile', 768, 480))
                        ->deletable()
                        ->removeUploadedFileButtonPosition('right')
                        ->deleteUploadedFileUsing(static fn (string $file): bool => Storage::disk('public')->delete($file))
                        ->openable()
                        ->downloadable()
                        ->helperText('Saved as an optimized WebP image, up to 768×480px. Leave empty to display the desktop banner.'),

                    TextInput::make('link_url')
                        ->label('Banner Link URL (Optional)')
                        ->placeholder('https://example.com/shop or /products')
                        ->maxLength(255)
                        ->helperText('If entered, the entire banner becomes clickable on the frontend.'),

                    Toggle::make('open_in_new_tab')
                        ->label('Open Link in New Tab')
                        ->default(false),
                ])->columns(2),

            // ==========================================
            // OPTION 2: ANIMATED CONTENT BANNER FORM
            // ==========================================
            Tabs::make('Animated Content Settings')
                ->visible(fn (Get $get) => $get('type') === 'animated_content')
                ->tabs([
                    // Tab A: Text Content
                    Tabs\Tab::make('Text Content')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('text_content.main_title')
                                    ->label('Main Title')
                                    ->placeholder('e.g., Bamboo Coffee Mug')
                                    ->required(fn (Get $get) => $get('type') === 'animated_content')
                                    ->maxLength(255),

                                TextInput::make('text_content.second_title')
                                    ->label('Second Title Line')
                                    ->placeholder('e.g., With Dual Opening Lid')
                                    ->maxLength(255),
                            ]),
                        ]),

                    // Tab B: Background
                    Tabs\Tab::make('Background')
                        ->icon('heroicon-o-paint-brush')
                        ->schema([
                            Select::make('background_settings.type')
                                ->label('Background Type')
                                ->options([
                                    'solid' => 'Solid Color',
                                    'gradient' => 'Color Gradient',
                                    'image' => 'Background Image / Texture',
                                ])
                                ->default('image')
                                ->live(),

                            Grid::make(3)
                                ->visible(fn (Get $get) => $get('background_settings.type') === 'gradient')
                                ->schema([
                                    ColorPicker::make('background_settings.gradient_from')
                                        ->label('Gradient Start')
                                        ->default('#ebf6ff'),

                                    ColorPicker::make('background_settings.gradient_to')
                                        ->label('Gradient End')
                                        ->default('#dbeafe'),

                                    Select::make('background_settings.gradient_direction')
                                        ->label('Gradient Angle')
                                        ->options([
                                            '135deg' => '135° (Diagonal)',
                                            '90deg' => '90° (Left to Right)',
                                            '180deg' => '180° (Top to Bottom)',
                                            '45deg' => '45° (Angled Up)',
                                        ])
                                        ->default('135deg'),
                                ]),

                            Grid::make(2)
                                ->visible(fn (Get $get) => $get('background_settings.type') === 'solid')
                                ->schema([
                                    ColorPicker::make('background_settings.bg_color')
                                        ->label('Background Color')
                                        ->default('#ebf6ff'),
                                ]),

                            Grid::make(2)
                                ->visible(fn (Get $get) => $get('background_settings.type') === 'image')
                                ->schema([
                                    FileUpload::make('background_settings.bg_image')
                                        ->label('Background Image')
                                        ->image()
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->disk('public')
                                        ->directory('sliders/backgrounds')
                                        ->visibility('public')
                                        ->imagePreviewHeight('200')
                                        ->imageResizeMode('contain')
                                        ->imageResizeTargetWidth('1920')
                                        ->imageResizeTargetHeight('800')
                                        ->imageResizeUpscale(false)
                                        ->saveUploadedFileUsing(static fn (TemporaryUploadedFile $file): ?string => app(ImageOptimizationService::class)
                                            ->storeAsWebp($file->getRealPath(), 'public', 'sliders/backgrounds', 1920, 800))
                                        ->deletable()
                                        ->removeUploadedFileButtonPosition('right')
                                        ->deleteUploadedFileUsing(static fn (string $file): bool => Storage::disk('public')->delete($file))
                                        ->helperText('Saved as an optimized WebP image, up to 1920×800px without cropping.'),

                                    FileUpload::make('background_settings.mobile_bg_image')
                                        ->label('Mobile Background Image')
                                        ->image()
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->disk('public')
                                        ->directory('sliders/backgrounds')
                                        ->visibility('public')
                                        ->imagePreviewHeight('200')
                                        ->imageResizeMode('contain')
                                        ->imageResizeTargetWidth('768')
                                        ->imageResizeTargetHeight('480')
                                        ->imageResizeUpscale(false)
                                        ->saveUploadedFileUsing(static fn (TemporaryUploadedFile $file): ?string => app(ImageOptimizationService::class)
                                            ->storeAsWebp($file->getRealPath(), 'public', 'sliders/backgrounds', 768, 480))
                                        ->deletable()
                                        ->removeUploadedFileButtonPosition('right')
                                        ->deleteUploadedFileUsing(static fn (string $file): bool => Storage::disk('public')->delete($file))
                                        ->helperText('Saved as an optimized WebP image, up to 768×480px without cropping.'),

                                    TextInput::make('background_settings.image_link')
                                        ->label('Background Image Link (Optional)')
                                        ->url()
                                        ->maxLength(2048)
                                        ->helperText('Use an external image URL. This takes priority over the uploaded image.'),

                                    Select::make('background_settings.overlay_opacity')
                                        ->label('Dark Overlay Opacity')
                                        ->options([
                                            '0' => '0% (Transparent)',
                                            '20' => '20% (Light)',
                                            '40' => '40% (Medium)',
                                            '60' => '60% (Dark)',
                                            '80' => '80% (Heavy)',
                                        ])
                                        ->default('40'),

                                    Select::make('background_settings.bg_position')
                                        ->label('Image Position')
                                        ->options([
                                            'center center' => 'Center Center',
                                            'top center' => 'Top Center',
                                            'bottom center' => 'Bottom Center',
                                        ])
                                        ->default('center center'),
                                ]),
                        ]),

                    // Tab E: GSAP Entrance Animations
                    Tabs\Tab::make('Layer Animations')
                        ->icon('heroicon-o-sparkles')
                        ->schema([
                            Section::make('Main Heading Entrance')->schema([
                                Select::make('layer_animations.title.effect')
                                    ->label('Animation')
                                    ->options(static::animationOptions())
                                    ->default('fade_up'),
                                TextInput::make('layer_animations.title.delay')
                                    ->label('Delay (sec)')
                                    ->numeric()
                                    ->default(0.25),
                                TextInput::make('layer_animations.title.duration')
                                    ->label('Duration (sec)')
                                    ->numeric()
                                    ->default(0.8),
                            ])->columns(3)->collapsible(),

                        ]),

                    // Tab F: Responsive Settings
                    Tabs\Tab::make('Responsive Settings')
                        ->icon('heroicon-o-device-phone-mobile')
                        ->schema([
                            Grid::make(3)->schema([
                                TextInput::make('responsive_settings.desktop_height')
                                    ->label('Desktop Height')
                                    ->default('650px')
                                    ->helperText('e.g., 650px or 700px'),

                                TextInput::make('responsive_settings.tablet_height')
                                    ->label('Tablet Height')
                                    ->default('520px'),

                                TextInput::make('responsive_settings.mobile_height')
                                    ->label('Mobile Height')
                                    ->default('460px'),
                            ]),

                            Grid::make(1)->schema([
                                Select::make('responsive_settings.mobile_text_alignment')
                                    ->label('Mobile Text Align')
                                    ->options([
                                        'center' => 'Center',
                                        'left' => 'Left',
                                        'right' => 'Right',
                                    ])
                                    ->default('center'),
                            ]),

                            Grid::make(1)->schema([
                                Select::make('responsive_settings.mobile_title_size')
                                    ->label('Mobile Heading Font Size')
                                    ->options([
                                        'text-2xl' => 'Medium (24px)',
                                        'text-3xl' => 'Large (30px)',
                                        'text-4xl' => 'Extra Large (36px)',
                                    ])
                                    ->default('text-3xl'),
                            ]),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function animationOptions(): array
    {
        return [
            'fade_in' => 'Fade In',
            'fade_up' => 'Fade Up',
            'fade_down' => 'Fade Down',
            'slide_left' => 'Slide Left',
            'slide_right' => 'Slide Right',
            'zoom_in' => 'Zoom In',
            'bounce_in' => 'Bounce In',
            'rotate_in' => 'Rotate In',
            'float' => 'Float',
            'none' => 'None',
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Preview')
                    ->getStateUsing(function (SliderItem $record): ?string {
                        if ($record->isSingleImage()) {
                            return $record->desktop_image_url;
                        }
                        return $record->background_image_url;
                    })
                    ->height(55)
                    ->extraImgAttributes([
                        'style' => 'max-height: 55px; width: auto; object-fit: contain; border-radius: 6px;',
                    ]),

                TextColumn::make('title')
                    ->label('Slide Name / Title')
                    ->searchable()
                    ->sortable()
                    ->description(function (SliderItem $record): string {
                        if ($record->isSingleImage()) {
                            return 'Single Image' . ($record->link_url ? ' • Links to ' . $record->link_url : '');
                        }
                        return $record->text_content['second_title'] ?? 'Animated Content Banner';
                    }),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'single_image' => 'Single Image',
                        'animated_content' => 'Animated Content',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'single_image' => 'gray',
                        'animated_content' => 'success',
                        default => 'info',
                    }),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Published'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add New Slide'),
            ])
            ->actions([
                EditAction::make(),
                ReplicateAction::make()
                    ->label('Duplicate')
                    ->excludeAttributes(['sort_order'])
                    ->beforeReplicaSaved(function (SliderItem $replica): void {
                        $replica->title = $replica->title . ' (Copy)';
                        $replica->sort_order = SliderItem::where('slider_id', $replica->slider_id)->max('sort_order') + 1;
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc');
    }
}
