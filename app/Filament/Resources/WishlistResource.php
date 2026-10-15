<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WishlistResource\Pages;
use App\Models\Wishlist;
use App\Support\Money;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WishlistResource extends Resource
{
    protected static ?string $model = Wishlist::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Customer'),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Product'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Wishlisted Product')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('product.price')
                    ->label('Price')
                    ->formatStateUsing(fn ($state) => Money::format($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added On')
                    ->dateTime('d M, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->requiresConfirmation(),
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
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'product']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWishlists::route('/'),
        ];
    }
}
