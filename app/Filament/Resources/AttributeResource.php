<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributeResource\Pages;
use App\Models\Attribute;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Attribute Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Attribute Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Size, Colour, Material')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, $old, $operation, Forms\Get $get, Forms\Set $set): void {
                                if ($operation === 'create' && filled($state) && (blank($get('slug')) || $get('slug') === Str::slug($old ?? ''))) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Attribute::class, 'slug', ignoreRecord: true)
                            ->helperText('Unique URL-friendly identifier.'),

                        Select::make('type')
                            ->label('Display Type')
                            ->options([
                                'select' => 'Select (Dropdown / Text Buttons)',
                                'color' => 'Colour Swatches (with Hex Code)',
                            ])
                            ->default('select')
                            ->required()
                            ->live(),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),

                Section::make('Attribute Values')
                    ->description('Define reusable values for this attribute (e.g. S, M, L or Red, Blue, Green).')
                    ->schema([
                        Repeater::make('values')
                            ->relationship('values')
                            ->schema([
                                TextInput::make('value')
                                    ->label('Value / Label')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, Forms\Get $get, Forms\Set $set): void {
                                        if (filled($state) && blank($get('slug'))) {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->columnSpan(4),

                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(3),

                                ColorPicker::make('color_code')
                                    ->label('Colour Swatch')
                                    ->helperText('Hex code for visual swatch')
                                    ->columnSpan(3),

                                TextInput::make('sort_order')
                                    ->label('Sort')
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(2),
                            ])
                            ->columns(12)
                            ->addActionLabel('Add New Value')
                            ->reorderable('sort_order')
                            ->collapsible()
                            ->cloneable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Attribute')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'color' => 'Colour Swatch',
                        default => 'Select',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'color' => 'warning',
                        default => 'info',
                    }),

                TextColumn::make('values.value')
                    ->label('Values')
                    ->badge()
                    ->separator(',')
                    ->limitList(6),

                TextColumn::make('sort_order')
                    ->label('Sort Order')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttributes::route('/'),
            'create' => Pages\CreateAttribute::route('/create'),
            'edit' => Pages\EditAttribute::route('/{record}/edit'),
        ];
    }
}
