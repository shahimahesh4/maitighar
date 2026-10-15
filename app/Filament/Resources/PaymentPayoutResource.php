<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentPayoutResource\Pages;
use App\Models\PaymentPayout;
use App\Support\Money;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentPayoutResource extends Resource
{
    protected static ?string $model = PaymentPayout::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Payment Settings';

    protected static ?string $navigationLabel = 'Gateway Payouts';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payout Information')
                    ->schema([
                        Forms\Components\TextInput::make('gateway_code')
                            ->disabled(),
                        Forms\Components\TextInput::make('gateway_payout_id')
                            ->label('Gateway Payout ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('currency')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('arrival_date')
                            ->disabled(),
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Payout Metadata')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('gateway_code')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gateway_payout_id')
                    ->label('Payout ID')
                    ->fontFamily('mono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn ($state) => Money::format($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('arrival_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gateway_code')
                    ->options([
                        'khalti' => 'Khalti',
                        'esewa' => 'eSewa',
                        'fonepay' => 'Fonepay',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentPayouts::route('/'),
        ];
    }
}
