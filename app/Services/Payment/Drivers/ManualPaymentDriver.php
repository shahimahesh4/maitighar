<?php

namespace App\Services\Payment\Drivers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\Payment\DataObjects\HealthResult;
use App\Services\Payment\DataObjects\PaymentResponse;
use App\Services\Payment\DataObjects\RefundResponse;
use App\Services\Payment\DataObjects\WebhookResult;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Http\Request;

class ManualPaymentDriver extends AbstractGatewayDriver
{
    public function purchase(Order $order, array $options = []): PaymentResponse
    {
        return PaymentResponse::make(
            success: true,
            status: 'pending_verification',
            transactionId: 'MANUAL-'.$order->id.'-'.strtoupper(bin2hex(random_bytes(3))),
            redirectUrl: route('success', ['order_id' => $order->id]),
        );
    }

    public function refund(PaymentTransaction $transaction, float $amount, string $reason): RefundResponse
    {
        return RefundResponse::make(false, 'failed', errorMessage: 'Manual payments must be refunded by bank transfer and recorded by an administrator.');
    }

    public function verifyWebhook(Request $request): WebhookResult
    {
        return WebhookResult::make(false);
    }

    public function syncPaymentStatus(PaymentTransaction $transaction): PaymentResponse
    {
        return PaymentResponse::make($transaction->status === 'completed', $transaction->status, $transaction->gateway_transaction_id);
    }

    public function checkHealth(): HealthResult
    {
        return HealthResult::make(true, 'Manual payment review workflow is available.');
    }

    public function getFormSchema(): array
    {
        return [
            Textarea::make('settings.instructions')->label('Customer Instructions'),
            TextInput::make('settings.account_name')->label('Account Name'),
            TextInput::make('settings.account_number')->label('Account Number'),
            TextInput::make('settings.bank_name')->label('Bank / Wallet Name'),
            FileUpload::make('settings.qr_image')->label('Payment QR Code')->image()->disk('public')->directory('payment-qr'),
        ];
    }

    public function getValidationRules(): array
    {
        return [];
    }
}
