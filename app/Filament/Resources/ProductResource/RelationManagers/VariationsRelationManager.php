<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Models\AttributeValue;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationAttributeValue;
use App\Support\Money;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VariationsRelationManager extends RelationManager
{
    protected static string $relationship = 'variations';

    protected static ?string $title = 'Product Variations';

    public function form(Form $form): Form
    {
        /** @var Product $product */
        $product = $this->getOwnerRecord();
        $variationAttributes = $product->productAttributes()
            ->where('is_variation', true)
            ->with(['attribute', 'values'])
            ->get();

        $attributeFields = [];
        foreach ($variationAttributes as $pa) {
            $attr = $pa->attribute;
            if (! $attr) {
                continue;
            }

            $options = $pa->values->pluck('value', 'id')->all();
            $attributeFields[] = Forms\Components\Select::make('attr_' . $attr->id)
                ->label($attr->name)
                ->options($options)
                ->required()
                ->loadStateUsing(function (?Model $record) use ($attr): ?int {
                    if (! $record) {
                        return null;
                    }

                    return $record->variationAttributeValues()
                        ->where('attribute_id', $attr->id)
                        ->value('attribute_value_id');
                });
        }

        return $form
            ->schema([
                Forms\Components\Section::make('Variation Attributes')
                    ->description('Select the attribute combination for this variation.')
                    ->schema($attributeFields)
                    ->columns(max(1, count($attributeFields))),

                Forms\Components\Section::make('Pricing & Inventory (AUD)')
                    ->schema([
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255)
                            ->unique(ProductVariation::class, 'sku', ignoreRecord: true)
                            ->placeholder('e.g. ' . ($product->sku ? $product->sku . '-VAR' : 'SKU-001')),

                        Forms\Components\TextInput::make('sale_price')
                            ->label('Sale Price (AUD)')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder(Money::format($product->sale_price))
                            ->helperText('Leave empty to inherit parent product sale price.'),

                        Forms\Components\TextInput::make('regular_price')
                            ->label('Regular Price (AUD)')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder(Money::format($product->regular_price))
                            ->helperText('Original price before discount.'),

                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('Stock Quantity')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Forms\Components\Toggle::make('manage_stock')
                            ->label('Manage Stock')
                            ->default(true),

                        Forms\Components\Toggle::make('is_enabled')
                            ->label('Enabled')
                            ->default(true),

                        Forms\Components\FileUpload::make('image')
                            ->label('Variation Image')
                            ->image()
                            ->disk('public')
                            ->directory('products/variations')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->helperText('Optional. If not set, falls back to the main product image.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        /** @var Product $product */
        $product = $this->getOwnerRecord();

        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->getStateUsing(fn (ProductVariation $record): string => $record->getImageUrl())
                    ->square(),

                Tables\Columns\TextColumn::make('combination_label')
                    ->label('Combination')
                    ->weight('bold')
                    ->searchable(query: function ($query, string $search) {
                        return $query->whereHas('variationAttributeValues.attributeValue', function ($q) use ($search) {
                            $q->where('value', 'like', "%{$search}%");
                        });
                    }),

                Tables\Columns\TextInputColumn::make('sku')
                    ->label('SKU')
                    ->rules(['nullable', 'string', 'max:255']),

                Tables\Columns\TextColumn::make('regular_price')
                    ->label('Regular (AUD)')
                    ->formatStateUsing(fn ($state) => $state !== null ? Money::format($state) : '-'),

                Tables\Columns\TextInputColumn::make('sale_price')
                    ->label('Price (AUD)')
                    ->rules(['nullable', 'numeric', 'min:0']),

                Tables\Columns\TextInputColumn::make('stock_quantity')
                    ->label('Stock')
                    ->rules(['required', 'integer', 'min:0']),

                Tables\Columns\ToggleColumn::make('is_enabled')
                    ->label('Enabled'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_enabled')
                    ->label('Enabled Status'),
            ])
            ->headerActions([
                // 1. Generate Variations Action
                Tables\Actions\Action::make('generateVariations')
                    ->label('Generate Variations')
                    ->icon('heroicon-o-sparkles')
                    ->color('primary')
                    ->modalHeading('Generate All Product Variations')
                    ->modalSubheading(function () use ($product): string {
                        $prep = $this->calculateVariationCombinations($product);
                        if (! empty($prep['error'])) {
                            return $prep['error'];
                        }

                        $msg = "Total possible combinations: {$prep['totalCount']}.\n"
                            . "Existing variations: {$prep['existingCount']}.\n"
                            . "New variations to generate: {$prep['newCount']}.\n\n"
                            . "Regenerating will only add missing combinations. All existing prices, stock, images, and SKUs will be completely preserved.";

                        if ($prep['totalCount'] > 50) {
                            $msg .= "\n\n⚠️ Safety limit exceeded: More than 50 combinations would be generated. Please refine your attribute values.";
                        }

                        return $msg;
                    })
                    ->requiresConfirmation()
                    ->modalSubmitActionLabel('Generate Missing Variations')
                    ->action(function () use ($product): void {
                        $prep = $this->calculateVariationCombinations($product);

                        if (! empty($prep['error'])) {
                            Notification::make()->danger()->title('Cannot Generate Variations')->body($prep['error'])->send();

                            return;
                        }

                        if ($prep['totalCount'] > 50) {
                            Notification::make()->danger()->title('Safety Limit Exceeded')->body('Cannot generate more than 50 variations at once. Please reduce your attribute values.')->send();

                            return;
                        }

                        if ($prep['newCount'] === 0) {
                            Notification::make()->info()->title('All Variations Exist')->body('No new variations to generate. All combinations already exist.')->send();

                            return;
                        }

                        DB::transaction(function () use ($product, $prep): void {
                            foreach ($prep['missingCombos'] as $combo) {
                                // Build a readable SKU suffix
                                $valueSlugs = [];
                                foreach ($combo as $attrId => $valId) {
                                    $val = AttributeValue::find($valId);
                                    if ($val) {
                                        $valueSlugs[] = Str::slug($val->value);
                                    }
                                }

                                $baseSku = $product->sku ?: 'PRD-' . $product->id;
                                $candidateSku = strtoupper($baseSku . '-' . implode('-', $valueSlugs));

                                // Ensure uniqueness
                                $counter = 1;
                                $finalSku = $candidateSku;
                                while (ProductVariation::where('sku', $finalSku)->exists()) {
                                    $finalSku = $candidateSku . '-' . $counter;
                                    $counter++;
                                }

                                $variation = ProductVariation::create([
                                    'product_id' => $product->id,
                                    'sku' => $finalSku,
                                    'sale_price' => $product->sale_price,
                                    'regular_price' => $product->regular_price,
                                    'stock_quantity' => 0,
                                    'manage_stock' => true,
                                    'is_enabled' => true,
                                ]);

                                foreach ($combo as $attrId => $valId) {
                                    ProductVariationAttributeValue::create([
                                        'product_variation_id' => $variation->id,
                                        'attribute_id' => $attrId,
                                        'attribute_value_id' => $valId,
                                    ]);
                                }
                            }

                            $product->syncPriceAndStockFromVariations();
                        });

                        Notification::make()
                            ->success()
                            ->title('Variations Generated')
                            ->body("Generated {$prep['newCount']} new variation(s) successfully! Existing records were preserved.")
                            ->send();
                    }),

                // 2. Manual Create Variation Action
                Tables\Actions\CreateAction::make()
                    ->label('Add Single Variation')
                    ->modalHeading('Create Custom Variation')
                    ->using(function (array $data) use ($product): ProductVariation {
                        $variationAttributes = $product->productAttributes()
                            ->where('is_variation', true)
                            ->get();

                        $combo = [];
                        foreach ($variationAttributes as $pa) {
                            $key = 'attr_' . $pa->attribute_id;
                            if (empty($data[$key])) {
                                throw ValidationException::withMessages([
                                    $key => 'Please select a value for ' . ($pa->attribute->name ?? 'Attribute'),
                                ]);
                            }
                            $combo[$pa->attribute_id] = (int) $data[$key];
                        }

                        // Prevent duplicate combination
                        if ($this->combinationExists($product, $combo)) {
                            throw ValidationException::withMessages([
                                'sku' => 'A variation with this exact attribute combination already exists for this product.',
                            ]);
                        }

                        return DB::transaction(function () use ($product, $data, $combo): ProductVariation {
                            $sku = !empty($data['sku']) ? trim((string) $data['sku']) : null;
                            if (blank($sku)) {
                                $valueSlugs = [];
                                foreach ($combo as $attrId => $valId) {
                                    $val = AttributeValue::find($valId);
                                    if ($val) {
                                        $valueSlugs[] = Str::slug($val->value);
                                    }
                                }
                                $baseSku = $product->sku ?: 'MG-AUS-' . $product->id;
                                $candidateSku = strtoupper($baseSku . ($valueSlugs ? '-' . implode('-', $valueSlugs) : '-VAR'));
                                $counter = 1;
                                $sku = $candidateSku;
                                while (ProductVariation::where('sku', $sku)->exists()) {
                                    $sku = $candidateSku . '-' . $counter;
                                    $counter++;
                                }
                            }

                            $variation = ProductVariation::create([
                                'product_id' => $product->id,
                                'sku' => $sku,
                                'sale_price' => $data['sale_price'] ?? $product->sale_price,
                                'regular_price' => $data['regular_price'] ?? $product->regular_price,
                                'stock_quantity' => (int) ($data['stock_quantity'] ?? 0),
                                'manage_stock' => (bool) ($data['manage_stock'] ?? true),
                                'image' => $data['image'] ?? null,
                                'is_enabled' => (bool) ($data['is_enabled'] ?? true),
                            ]);

                            foreach ($combo as $attrId => $valId) {
                                ProductVariationAttributeValue::create([
                                    'product_variation_id' => $variation->id,
                                    'attribute_id' => $attrId,
                                    'attribute_value_id' => $valId,
                                ]);
                            }

                            $product->syncPriceAndStockFromVariations();

                            return $variation;
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->using(function (Model $record, array $data) use ($product): Model {
                        $variationAttributes = $product->productAttributes()
                            ->where('is_variation', true)
                            ->get();

                        $combo = [];
                        foreach ($variationAttributes as $pa) {
                            $key = 'attr_' . $pa->attribute_id;
                            if (! empty($data[$key])) {
                                $combo[$pa->attribute_id] = (int) $data[$key];
                            }
                        }

                        // Check uniqueness if combination changed
                        if (! empty($combo) && $this->combinationExists($product, $combo, $record->id)) {
                            throw ValidationException::withMessages([
                                'sku' => 'Another variation with this attribute combination already exists.',
                            ]);
                        }

                        DB::transaction(function () use ($record, $data, $combo, $product): void {
                            $record->update([
                                'sku' => $data['sku'] ?? null,
                                'sale_price' => $data['sale_price'] ?? null,
                                'regular_price' => $data['regular_price'] ?? null,
                                'stock_quantity' => (int) ($data['stock_quantity'] ?? 0),
                                'manage_stock' => (bool) ($data['manage_stock'] ?? true),
                                'image' => $data['image'] ?? null,
                                'is_enabled' => (bool) ($data['is_enabled'] ?? true),
                            ]);

                            if (! empty($combo)) {
                                $record->variationAttributeValues()->delete();
                                foreach ($combo as $attrId => $valId) {
                                    ProductVariationAttributeValue::create([
                                        'product_variation_id' => $record->id,
                                        'attribute_id' => $attrId,
                                        'attribute_value_id' => $valId,
                                    ]);
                                }
                            }

                            $product->syncPriceAndStockFromVariations();
                        });

                        return $record;
                    }),

                Tables\Actions\DeleteAction::make()
                    ->modalDescription('Deleting this variation will safely preserve order history (historical order items keep their SKU and attribute snapshot).')
                    ->after(fn () => $product->syncPriceAndStockFromVariations()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // 1. Bulk Update Prices
                    Tables\Actions\BulkAction::make('bulkUpdatePrices')
                        ->label('Update Prices')
                        ->icon('heroicon-o-currency-dollar')
                        ->form([
                            Forms\Components\TextInput::make('sale_price')
                                ->label('New Sale Price (AUD)')
                                ->numeric()
                                ->prefix('$')
                                ->required(),
                            Forms\Components\TextInput::make('regular_price')
                                ->label('New Regular Price (AUD)')
                                ->numeric()
                                ->prefix('$'),
                        ])
                        ->action(function (Collection $records, array $data) use ($product): void {
                            foreach ($records as $variation) {
                                $variation->update([
                                    'sale_price' => $data['sale_price'],
                                    'regular_price' => $data['regular_price'] ?: $data['sale_price'],
                                ]);
                            }
                            $product->syncPriceAndStockFromVariations();
                            Notification::make()->success()->title('Prices updated for selected variations.')->send();
                        }),

                    // 2. Bulk Update Stock
                    Tables\Actions\BulkAction::make('bulkUpdateStock')
                        ->label('Update Stock')
                        ->icon('heroicon-o-archive-box')
                        ->form([
                            Forms\Components\TextInput::make('stock_quantity')
                                ->label('New Stock Quantity')
                                ->numeric()
                                ->default(0)
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) use ($product): void {
                            foreach ($records as $variation) {
                                $diff = (int) $data['stock_quantity'] - (int) $variation->stock_quantity;
                                $variation->update([
                                    'stock_quantity' => (int) $data['stock_quantity'],
                                ]);

                                if ($diff !== 0) {
                                    InventoryLog::create([
                                        'product_id' => $product->id,
                                        'variation_id' => $variation->id,
                                        'user_id' => auth()->id(),
                                        'quantity_change' => $diff,
                                        'reason' => 'Bulk stock adjustment in admin panel',
                                    ]);
                                }
                            }
                            $product->syncPriceAndStockFromVariations();
                            Notification::make()->success()->title('Stock updated for selected variations.')->send();
                        }),

                    // 3. Bulk Enable
                    Tables\Actions\BulkAction::make('bulkEnable')
                        ->label('Enable Selected')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (Collection $records) use ($product): void {
                            $records->each(fn (ProductVariation $v) => $v->update(['is_enabled' => true]));
                            $product->syncPriceAndStockFromVariations();
                            Notification::make()->success()->title('Selected variations enabled.')->send();
                        }),

                    // 4. Bulk Disable
                    Tables\Actions\BulkAction::make('bulkDisable')
                        ->label('Disable Selected')
                        ->icon('heroicon-o-x-circle')
                        ->action(function (Collection $records) use ($product): void {
                            $records->each(fn (ProductVariation $v) => $v->update(['is_enabled' => false]));
                            $product->syncPriceAndStockFromVariations();
                            Notification::make()->success()->title('Selected variations disabled.')->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make()
                        ->after(fn () => $product->syncPriceAndStockFromVariations()),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    /**
     * Check if a combination already exists for this product.
     *
     * @param array<int, int> $combo [attribute_id => attribute_value_id]
     */
    private function combinationExists(Product $product, array $combo, ?int $ignoreVariationId = null): bool
    {
        ksort($combo);
        $variations = $product->variations()->with('variationAttributeValues')->get();

        foreach ($variations as $v) {
            if ($ignoreVariationId && $v->id === $ignoreVariationId) {
                continue;
            }

            $existingMap = [];
            foreach ($v->variationAttributeValues as $vav) {
                $existingMap[$vav->attribute_id] = (int) $vav->attribute_value_id;
            }
            ksort($existingMap);

            if ($existingMap == $combo) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate Cartesian product and identify missing combinations.
     *
     * @return array{error?: string, totalCount: int, existingCount: int, newCount: int, missingCombos: array}
     */
    private function calculateVariationCombinations(Product $product): array
    {
        $productAttributes = $product->productAttributes()
            ->where('is_variation', true)
            ->with(['attribute', 'values'])
            ->get();

        if ($productAttributes->isEmpty()) {
            return [
                'error' => 'No variation attributes found. In the "Product Attributes" tab, assign attributes and enable "Used for Variations".',
                'totalCount' => 0,
                'existingCount' => 0,
                'newCount' => 0,
                'missingCombos' => [],
            ];
        }

        $matrix = [];
        foreach ($productAttributes as $pa) {
            $valueIds = $pa->values->pluck('id')->all();
            if (empty($valueIds)) {
                return [
                    'error' => "Attribute '{$pa->attribute->name}' has no values selected. Please select at least one value in the Product Attributes tab.",
                    'totalCount' => 0,
                    'existingCount' => 0,
                    'newCount' => 0,
                    'missingCombos' => [],
                ];
            }
            $matrix[$pa->attribute_id] = $valueIds;
        }

        // Cartesian product
        $combinations = [[]];
        foreach ($matrix as $attrId => $valIds) {
            $append = [];
            foreach ($combinations as $combo) {
                foreach ($valIds as $valId) {
                    $append[] = $combo + [$attrId => $valId];
                }
            }
            $combinations = $append;
        }

        $totalCount = count($combinations);

        // Fetch existing variation combinations
        $existingVariations = $product->variations()->with('variationAttributeValues')->get();
        $existingSignatures = [];
        foreach ($existingVariations as $v) {
            $map = [];
            foreach ($v->variationAttributeValues as $vav) {
                $map[$vav->attribute_id] = (int) $vav->attribute_value_id;
            }
            ksort($map);
            $existingSignatures[json_encode($map)] = true;
        }

        $missingCombos = [];
        foreach ($combinations as $combo) {
            ksort($combo);
            $sig = json_encode($combo);
            if (! isset($existingSignatures[$sig])) {
                $missingCombos[] = $combo;
            }
        }

        return [
            'totalCount' => $totalCount,
            'existingCount' => count($existingSignatures),
            'newCount' => count($missingCombos),
            'missingCombos' => $missingCombos,
        ];
    }
}
