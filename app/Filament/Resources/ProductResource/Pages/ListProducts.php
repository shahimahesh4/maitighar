<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All Products')
                ->badge(Product::withTrashed()->count()),
            'published' => Tab::make('Published')
                ->badge(Product::query()->where('is_active', true)->count())
                ->query(fn ($query) => $query->whereNull('deleted_at')->where('is_active', true)),
            'unpublished' => Tab::make('Unpublished')
                ->badge(Product::query()->where('is_active', false)->count())
                ->query(fn ($query) => $query->whereNull('deleted_at')->where('is_active', false)),
            'trash' => Tab::make('Trash')
                ->badge(Product::onlyTrashed()->count())
                ->badgeColor('danger')
                ->query(fn ($query) => $query->whereNotNull('deleted_at')),
        ];
    }
}
