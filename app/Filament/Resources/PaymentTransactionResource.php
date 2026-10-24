<?php

namespace App\Filament\Resources;

use App\Events\RefundProcessed;
use App\Filament\Resources\PaymentTransactionResource\Pages;
use App\Models\PaymentRefund;
use App\Models\PaymentTransaction;
use App\Services\Payment\ManualPaymentReviewService;
use App\Services\Payment\PaymentGatewayManager;
use App\Support\Money;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PaymentTransactionResource extends Resource
{
    protected static ?string $model = PaymentTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Payment Settings';

    protected static ?string $navigationLabel = 'Transactions Log';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\TextInput::make('order_id')
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('user.name')
                            ->label('Customer')
                            ->disabled(),
                        Forms\Components\TextInput::make('gateway_code')
                            ->disabled(),
                        Forms\Components\TextInput::make('gateway_transaction_id')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('currency')
                            ->disabled(),
                        Forms\Components\TextInput::make('payment_reference')->label('Customer Reference')->disabled(),
                        Forms\Components\Placeholder::make('voucher_path')
                            ->label('Payment Voucher')
                            ->content(fn (?PaymentTransaction $record): HtmlString|string => $record?->voucher_path
                                ? new HtmlString('<a class="text-primary-600 underline" target="_blank" rel="noopener" href="'.e(route('payment-vouchers.show', $record)).'">View protected receipt</a>')
                                : 'No receipt submitted'),
                        Forms\Components\TextInput::make('proof_submitted_at')->disabled(),
                        Forms\Components\TextInput::make('verifier.name')->label('Verified By')->disabled(),
                        Forms\Components\TextInput::make('verified_at')->disabled(),
                        Forms\Components\Textarea::make('rejection_reason')->disabled()->columnSpanFull(),
                        Forms\Components\TextInput::make('status')
                            ->disabled(),
                        Forms\Components\TextInput::make('type')
                            ->disabled(),
                        Forms\Components\Textarea::make('error_message')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\KeyValue::make('payload')
                            ->label('Raw Payload Response')
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
                Tables\Columns\TextColumn::make('order.id')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gateway_code')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gateway_transaction_id')
                    ->label('Gateway ID')
                    ->fontFamily('mono')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('payment_reference')->label('Customer Ref')->searchable()->toggleable(),
                Tables\Columns\IconColumn::make('voucher_path')
                    ->label('Voucher')
                    ->boolean()
                    ->getStateUsing(fn (PaymentTransaction $record): bool => filled($record->voucher_path))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn ($state, PaymentTransaction $record) => Money::format($state, $record->currency ?? 'AUD'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'refunded' => 'info',
                        'partially_refunded' => 'warning',
                        'failed' => 'danger',
                        'pending' => 'gray',
                        'pending_verification' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gateway_code')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'cod' => 'Cash on Delivery',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'pending_verification' => 'Pending Verification',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('viewVoucher')
                    ->label('View Voucher')
                    ->icon('heroicon-o-photo')
                    ->url(fn (PaymentTransaction $record) => $record->voucher_path ? route('payment-vouchers.show', $record) : null)
                    ->openUrlInNewTab()
                    ->visible(fn (PaymentTransaction $record) => filled($record->voucher_path)),

                Tables\Actions\Action::make('approveManualPayment')
                    ->label('Approve Payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Confirm that the voucher and payment reference match the received funds.')
                    ->action(function (PaymentTransaction $record) {
                        app(ManualPaymentReviewService::class)->approve($record, auth()->id());
                        Notification::make()->success()->title('Manual payment approved')->send();
                    })
                    ->visible(fn (PaymentTransaction $record) => $record->gateway_code === 'bank_transfer' && in_array($record->status, ['pending', 'pending_verification'], true)),

                Tables\Actions\Action::make('rejectManualPayment')
                    ->label('Reject Payment')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('reason')->label('Rejection Reason')->required()->maxLength(1000),
                    ])
                    ->action(function (PaymentTransaction $record, array $data) {
                        app(ManualPaymentReviewService::class)->reject($record, auth()->id(), $data['reason']);
                        Notification::make()->danger()->title('Manual payment rejected')->send();
                    })
                    ->visible(fn (PaymentTransaction $record) => $record->gateway_code === 'bank_transfer' && in_array($record->status, ['pending', 'pending_verification'], true)),
                // Manual Sync Action
                Tables\Actions\Action::make('syncStatus')
                    ->label('Sync Gateway')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function (PaymentTransaction $record) {
                        $manager = app(PaymentGatewayManager::class);
                        try {
                            $driver = $manager->driver($record->gateway_code);
                            $response = $driver->syncPaymentStatus($record);

                            if ($response->success) {
                                $record->update([
                                    'status' => $response->status,
                                    'payload' => array_merge($record->payload ?? [], $response->rawPayload),
                                ]);

                                // Sync Order payment status if completed
                                if ($response->status === 'completed') {
                                    $record->order->update([
                                        'payment_status' => 'paid',
                                        'status' => 'processing',
                                    ]);
                                }

                                Notification::make()
                                    ->success()
                                    ->title('Status Synced Successfully')
                                    ->body("Gateway status: {$response->status}")
                                    ->send();
                            } else {
                                Notification::make()
                                    ->danger()
                                    ->title('Sync Failed')
                                    ->body($response->errorMessage)
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Sync Failed')
                                ->body($e->getMessage())
                                ->send();
                        }
                    })
                    ->visible(fn (PaymentTransaction $record) => $record->status === 'pending'),

                // Issue Refund Action
                Tables\Actions\Action::make('refund')
                    ->label('Refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->form([
                        Forms\Components\TextInput::make('refund_amount')
                            ->label('Refund Amount')
                            ->numeric()
                            ->required()
                            ->helperText(fn (PaymentTransaction $record) => 'Max refundable amount: '.Money::format($record->amount)),
                        Forms\Components\TextInput::make('reason')
                            ->label('Reason')
                            ->required(),
                    ])
                    ->action(function (PaymentTransaction $record, array $data) {
                        $refundAmount = (float) $data['refund_amount'];

                        if ($refundAmount <= 0 || $refundAmount > $record->amount) {
                            Notification::make()
                                ->danger()
                                ->title('Invalid Amount')
                                ->body('Refund amount cannot exceed the transaction amount.')
                                ->send();

                            return;
                        }

                        $manager = app(PaymentGatewayManager::class);
                        try {
                            $driver = $manager->driver($record->gateway_code);

                            // Create initial pending refund model
                            $refundModel = PaymentRefund::create([
                                'transaction_id' => $record->id,
                                'order_id' => $record->order_id,
                                'amount' => $refundAmount,
                                'reason' => $data['reason'],
                                'status' => 'pending',
                            ]);

                            $response = $driver->refund($record, $refundAmount, $data['reason']);

                            if ($response->success) {
                                $refundModel->update([
                                    'gateway_refund_id' => $response->refundId,
                                    'status' => 'processed',
                                    'metadata' => $response->rawPayload,
                                ]);

                                // Update transaction and order status
                                $newStatus = ($refundAmount == $record->amount) ? 'refunded' : 'partially_refunded';
                                $record->update(['status' => $newStatus]);
                                $record->order->update(['payment_status' => $newStatus]);

                                // Fire refund event
                                event(new RefundProcessed($refundModel));

                                Notification::make()
                                    ->success()
                                    ->title('Refund Processed')
                                    ->body('')
                                    ->send();
                            } else {
                                $refundModel->update(['status' => 'failed', 'metadata' => $response->rawPayload]);
                                Notification::make()
                                    ->danger()
                                    ->title('Refund Failed')
                                    ->body($response->errorMessage)
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Refund Process Failed')
                                ->body($e->getMessage())
                                ->send();
                        }
                    })
                    ->visible(fn (PaymentTransaction $record) => $record->status === 'completed' && $record->type === 'payment'),
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
            'index' => Pages\ListPaymentTransactions::route('/'),
        ];
    }
}
