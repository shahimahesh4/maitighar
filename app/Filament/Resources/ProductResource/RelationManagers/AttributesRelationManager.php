<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductAttribute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AttributesRelationManager extends RelationManager
{
    protected static string $relationship = 'productAttributes';

    protected static ?string $title = 'Product Attributes';

    public function form(Form $form): Form
    {
        $productId = $this->getOwnerRecord()->id;

        return $form
            ->schema([
                Forms\Components\Select::make('attribute_id')
                    ->label('Attribute')
                    ->options(function (?Model $record) use ($productId): array {
                        $query = Attribute::query()->orderBy('sort_order');
                        if (! $record) {
                            $alreadyAssigned = ProductAttribute::where('product_id', $productId)->pluck('attribute_id');
                            $query->whereNotIn('id', $alreadyAssigned);
                        }

                        return $query->pluck('name', 'id')->all();
                    })
                    ->required()
                    ->live()
                    ->disabled(fn (?Model $record): bool => $record !== null),

                Forms\Components\Select::make('values')
                    ->label('Selected Attribute Values')
                    ->multiple()
                    ->required()
                    ->options(function (Forms\Get $get, ?Model $record): array {
                        $attrId = $get('attribute_id') ?: $record?->attribute_id;
                        if (! $attrId) {
                            return [];
                        }

                        return AttributeValue::where('attribute_id', $attrId)
                            ->orderBy('sort_order')
                            ->pluck('value', 'id')
                            ->all();
                    })
                    ->loadStateUsing(function (?Model $record): array {
                        if (! $record) {
                            return [];
                        }

                        return $record->values()->pluck('attribute_values.id')->all();
                    })
                    ->saveRelationshipsUsing(function (Model $record, $state): void {
                        $record->values()->sync($state ?? []);
                    })
                    ->helperText('Select all the values available for this product.')
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_visible')
                    ->label('Visible on Product Page')
                    ->helperText('Display in the specifications or additional details on the product page.')
                    ->default(true),

                Forms\Components\Toggle::make('is_variation')
                    ->label('Used for Variations')
                    ->helperText('Enable this attribute to generate purchasable variations (e.g. Size, Colour).')
                    ->default(true),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('attribute.name')
            ->columns([
                Tables\Columns\TextColumn::make('attribute.name')
                    ->label('Attribute')
                    ->weight('bold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('values.value')
                    ->label('Assigned Values')
                    ->badge()
                    ->separator(',')
                    ->limitList(6),

                Tables\Columns\ToggleColumn::make('is_visible')
                    ->label('Visible on Page'),

                Tables\Columns\ToggleColumn::make('is_variation')
                    ->label('Used for Variations'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sort')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Attribute')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['product_id'] = $this->getOwnerRecord()->id;

                        return $data;
                    })
                    ->after(function (): void {
                        Notification::make()
                            ->success()
                            ->title('Attribute Added')
                            ->body('Attribute assigned to product. If this attribute is used for variations, visit the Variations tab to generate or update variations.')
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalDescription('Removing an attribute will not delete existing orders. If variations use this attribute, consider disabling or updating those variations.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
