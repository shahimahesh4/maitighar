<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Support\Money;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OutOfStockProducts extends BaseWidget
{
    protected int|string|array $columnSpan = 'md';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()->where('in_stock', false)
            )
            ->heading('Out of Stock Products')
            ->description('Inventory alert: products marked out of stock')
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable()
                    ->limit(35),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->fontFamily('mono')
                    ->searchable(),
                TextColumn::make('price')
                    ->formatStateUsing(fn ($state) => Money::format($state))
                    ->sortable(),
            ]);
    }
}
