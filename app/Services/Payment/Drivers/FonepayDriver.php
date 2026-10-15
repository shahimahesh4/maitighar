<?php

namespace App\Services\Payment\Drivers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\Payment\DataObjects\HealthResult;
use App\Services\Payment\DataObjects\PaymentResponse;
use App\Services\Payment\DataObjects\RefundResponse;
use App\Services\Payment\DataObjects\WebhookResult;
use Filament\Forms\Components\Placeholder;
use Illuminate\Http\Request;

class FonepayDriver extends AbstractGatewayDriver
{
    public function purchase(Order $order, array $options = []): PaymentResponse
    {
        return PaymentResponse::make(false, 'failed', errorMessage: 'Fonepay merchant API onboarding is required before this gateway can be activated.');
    }

    public function refund(PaymentTransaction $transaction, float $amount, string $reason): RefundResponse
    {
        return RefundResponse::make(false, 'failed', errorMessage: 'Process Fonepay refunds through your acquiring bank or merchant portal.');
    }

    public function verifyWebhook(Request $request): WebhookResult
    {
        return WebhookResult::make(false);
    }

    public function syncPaymentStatus(PaymentTransaction $transaction): PaymentResponse
    {
        return PaymentResponse::make(false, 'failed', $transaction->gateway_transaction_id, errorMessage: 'Fonepay merchant API is not configured.');
    }

    public function checkHealth(): HealthResult
    {
        return HealthResult::make(false, 'Fonepay merchant onboarding and private API credentials are required.');
    }

    public function getFormSchema(): array
    {
        return [Placeholder::make('onboarding')->label('Fonepay onboarding required')->content('Request e-commerce merchant API credentials and integration specifications from Fonepay or your acquiring bank before activation.')];
    }

    public function getValidationRules(): array
    {
        return [];
    }
}
