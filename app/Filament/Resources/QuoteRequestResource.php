<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteRequestResource\Pages;
use App\Models\QuoteRequest;
use App\Services\CommunicationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationLabel = 'Quote Requests';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Product requested')
                ->schema([
                    Forms\Components\Select::make('product_id')
                        ->relationship('product', 'name')
                        ->disabled()
                        ->dehydrated(false)
                        ->label('Product'),
                    Forms\Components\TextInput::make('quantity')
                        ->disabled()
                        ->label('Quantity'),
                ])->columns(2),

            Forms\Components\Section::make('Customer details')
                ->schema([
                    Forms\Components\TextInput::make('full_name')->disabled()->label('Full name'),
                    Forms\Components\TextInput::make('email')->email()->disabled()->label('Email address'),
                    Forms\Components\TextInput::make('phone')->disabled()->label('Phone'),
                    Forms\Components\TextInput::make('city')->disabled()->label('City'),
                    Forms\Components\Textarea::make('address')->disabled()->rows(2)->columnSpanFull()->label('Address'),
                ])->columns(2),

            Forms\Components\Section::make('Request message & status')
                ->schema([
                    Forms\Components\Textarea::make('message')
                        ->disabled()
                        ->rows(5)
                        ->placeholder('No additional requirements provided.')
                        ->columnSpanFull()
                        ->label('Customer message'),
                    Forms\Components\Select::make('status')
                        ->options([
                            'new' => 'New',
                            'contacted' => 'Contacted',
                            'quoted' => 'Quote Sent',
                            'closed' => 'Closed',
                        ])
                        ->required()
                        ->default('new'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->limit(35),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),
                Tables\Columns\TextColumn::make('phone')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('city')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('quantity')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'danger',
                        'contacted' => 'warning',
                        'quoted' => 'info',
                        'closed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'contacted' => 'Contacted',
                        'quoted' => 'Quote Sent',
                        'closed' => 'Closed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                Tables\Actions\Action::make('view_email')
                    ->label('View email')
                    ->icon('heroicon-o-envelope-open')
                    ->modalHeading('Quote request email details')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn (QuoteRequest $record) => view('filament.quote-requests.email-preview', ['record' => $record])),
                Tables\Actions\EditAction::make()->label('Edit'),
                Tables\Actions\Action::make('send_email')
                    ->label('Send email')
                    ->icon('heroicon-o-envelope')
                    ->color('primary')
                    ->form([
                        Forms\Components\TagsInput::make('recipients')
                            ->label('Recipients')
                            ->default(fn (QuoteRequest $record): array => [$record->email])
                            ->nestedRecursiveRules(['email'])
                            ->required(),
                        Forms\Components\TextInput::make('subject')
                            ->default(fn (QuoteRequest $record): string => 'Your quote request: '.$record->product->name)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('message')
                            ->label('Message')
                            ->default('Thank you for your enquiry. Our team is reviewing your request and will be in touch shortly.')
                            ->required()
                            ->rows(5),
                    ])
                    ->action(function (QuoteRequest $record, array $data): void {
                        $details = [
                            'Product' => $record->product->name,
                            'SKU' => $record->product->sku ?: '—',
                            'Quantity' => (string) $record->quantity,
                            'City' => $record->city,
                            'Address' => $record->address,
                        ];

                        $recipients = array_unique(array_filter($data['recipients'] ?? [], fn (mixed $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false));

                        foreach ($recipients as $email) {
                            app(CommunicationService::class)->send(
                                $email,
                                $data['subject'],
                                'Update about your quote request',
                                'Here is an update from the Maitighar team.',
                                $details,
                                null,
                                null,
                                $data['message'],
                            );
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Email sent')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->requiresConfirmation(),
                ])->icon('heroicon-m-ellipsis-vertical'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuoteRequests::route('/'),
            'edit' => Pages\EditQuoteRequest::route('/{record}/edit'),
        ];
    }
}
