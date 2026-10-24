<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers\SliderItemsRelationManager;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationGroup = 'Website';
    protected static ?string $navigationLabel = 'Slider Management';
    protected static ?string $modelLabel = 'Slider';
    protected static ?string $pluralModelLabel = 'Slider Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Slider Details')
                ->description('Configure the slider name and unique code used to place it on pages.')
                ->schema([
                    TextInput::make('name')
                        ->label('Slider Name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g., Homepage Hero Slider'),

                    TextInput::make('code')
                        ->label('Embed Code / Location Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->placeholder('e.g., homepage-hero')
                        ->helperText('Embed in any Blade template with: <livewire:slider location="homepage-hero" />'),

                    Textarea::make('description')
                        ->label('Description / Notes (Optional)')
                        ->rows(2)
                        ->columnSpanFull()
                        ->placeholder('Internal description of where and how this slider is displayed.'),

                    Toggle::make('is_active')
                        ->label('Slider Active')
                        ->default(true)
                        ->inline(false)
                        ->helperText('Quickly toggle this entire slider on or off on the frontend.'),
                ])->columns(2),

            Section::make('Playback & Navigation Controls')
                ->description('Revolution Slider playback options, timings, and navigation controls.')
                ->schema([
                    Grid::make(3)->schema([
                        Toggle::make('autoplay')
                            ->label('Enable Autoplay')
                            ->default(true)
                            ->inline(false),

                        TextInput::make('autoplay_speed')
                            ->label('Autoplay Delay (ms)')
                            ->numeric()
                            ->minValue(1500)
                            ->maxValue(30000)
                            ->default(5000)
                            ->helperText('5000ms = 5 seconds between slides.'),

                        TextInput::make('transition_speed')
                            ->label('Slide Transition Duration (ms)')
                            ->numeric()
                            ->minValue(300)
                            ->maxValue(5000)
                            ->default(800)
                            ->helperText('800ms gives a smooth transition.'),
                    ]),

                    Grid::make(4)->schema([
                        Select::make('transition_effect')
                            ->label('Transition Animation Effect')
                            ->options([
                                'fade' => 'Crossfade (Smooth Opacity)',
                                'slide' => 'Slide (Horizontal Wipe)',
                            ])
                            ->default('fade')
                            ->required(),

                        Toggle::make('pause_on_hover')
                            ->label('Pause On Hover')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Pauses autoplay when user hovers mouse over the banner.'),

                        Toggle::make('show_navigation')
                            ->label('Navigation Arrows')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Show previous and next arrow buttons.'),

                        Toggle::make('show_pagination')
                            ->label('Pagination Dots')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Show clickable slide indicators at the bottom.'),
                    ]),
                ]),

            Section::make('Canvas Heights (Responsive Dimensions)')
                ->description('Specify default banner canvas heights for different screen viewports.')
                ->schema([
                    TextInput::make('desktop_height')
                        ->label('Desktop Height')
                        ->default('650px')
                        ->helperText('e.g., 650px, 800px, or auto'),

                    TextInput::make('tablet_height')
                        ->label('Tablet Height')
                        ->default('520px')
                        ->helperText('Screen width <= 1024px'),

                    TextInput::make('mobile_height')
                        ->label('Mobile Height')
                        ->default('460px')
                        ->helperText('Screen width <= 640px'),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Slider Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('code')
                    ->label('Embed Code')
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->copyMessage('Embed code copied')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('items_count')
                    ->label('Total Slides')
                    ->counts('items')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('transition_effect')
                    ->label('Transition')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                ToggleColumn::make('autoplay')
                    ->label('Autoplay'),

                ToggleColumn::make('is_active')
                    ->label('Active'),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Action::make('preview')
                    ->label('Live Preview')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->color('warning')
                    ->modalHeading(fn (Slider $record): string => "Live Preview: {$record->name}")
                    ->modalWidth(MaxWidth::SevenExtraLarge)
                    ->modalContent(fn (Slider $record) => view('filament.slider-preview-modal', ['slider' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close Preview'),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SliderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
