<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeSliderResource\Pages;
use App\Models\HomeSlider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomeSliderResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $model = HomeSlider::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Website';
    protected static ?string $navigationLabel = 'Homepage Sliders';
    protected static ?string $modelLabel = 'homepage slide';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Slide Content')
                ->description('Text appears on the right side of the homepage banner.')
                ->schema([
                    Forms\Components\TextInput::make('name')->label('Admin name')->required()->maxLength(255)->helperText('Used only to identify this slide in the admin panel.'),
                    Forms\Components\TextInput::make('eyebrow')->maxLength(255)->placeholder('Authentic • Handcrafted • Timeless'),
                    Forms\Components\TextInput::make('title')->required()->maxLength(255),
                    Forms\Components\Textarea::make('subtitle')->rows(2)->maxLength(255)->columnSpanFull(),
                ])->columns(2),
            Forms\Components\Section::make('Banner Images')
                ->description('Upload full banner images in their actual dimensions (e.g. 1024×400, 1920×600). Banners will display at their full natural aspect ratio without any cropping.')
                ->schema([
                    Forms\Components\FileUpload::make('desktop_image')
                        ->label('Desktop / tablet image')
                        ->required()
                        ->image()
                        ->disk('public')
                        ->directory('sliders')
                        ->visibility('public')
                        ->imagePreviewHeight('220')
                        ->openable()
                        ->downloadable()
                        ->helperText('Upload full banner image. Actual image dimensions and aspect ratio are preserved without cropping.'),
                    Forms\Components\FileUpload::make('mobile_image')
                        ->label('Optional mobile image')
                        ->image()
                        ->disk('public')
                        ->directory('sliders/mobile')
                        ->visibility('public')
                        ->imagePreviewHeight('220')
                        ->openable()
                        ->downloadable()
                        ->helperText('Optional mobile image. Leave empty to use desktop image.'),
                ])->columns(2),
            Forms\Components\Section::make('Button & Display')
                ->schema([
                    Forms\Components\TextInput::make('button_label')->maxLength(80)->placeholder('Book an Appointment'),
                    Forms\Components\TextInput::make('button_url')->label('Button URL')->maxLength(255)->placeholder('/contact or https://...'),
                    Forms\Components\TextInput::make('duration')->label('Display duration (milliseconds)')->numeric()->minValue(3000)->maxValue(20000)->default(6000)->required(),
                    Forms\Components\TextInput::make('sort_order')->numeric()->minValue(0)->default(0)->required(),
                    Forms\Components\Toggle::make('is_active')->label('Published')->default(true)->inline(false),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('desktop_image')
                    ->label('Banner Preview')
                    ->getStateUsing(fn (HomeSlider $record): string => $record->desktopImageUrl())
                    ->height(65)
                    ->extraImgAttributes([
                        'style' => 'max-height: 65px; width: auto; object-fit: contain; border-radius: 6px;',
                    ]),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->description(fn (HomeSlider $record): string => $record->title),
                Tables\Columns\TextColumn::make('sort_order')->label('Order')->sortable(),
                Tables\Columns\TextColumn::make('duration')->formatStateUsing(fn (int $state): string => number_format($state / 1000, 1).' sec'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Published'),
                Tables\Columns\TextColumn::make('updated_at')->since()->sortable(),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeSliders::route('/'),
            'create' => Pages\CreateHomeSlider::route('/create'),
            'edit' => Pages\EditHomeSlider::route('/{record}/edit'),
        ];
    }
}
