<?php

namespace App\Services\Payment\Drivers;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\Payment\DataObjects\HealthResult;
use App\Services\Payment\DataObjects\PaymentResponse;
use App\Services\Payment\DataObjects\RefundResponse;
use App\Services\Payment\DataObjects\WebhookResult;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EsewaDriver extends AbstractGatewayDriver
{
    private function formUrl(): string
    {
        return $this->isLive() ? 'https://epay.esewa.com.np/api/epay/main/v2/form' : 'https://rc-epay.esewa.com.np/api/epay/main/v2/form';
    }

    private function statusUrl(): string
    {
        return $this->isLive() ? 'https://esewa.com.np/api/epay/transaction/status/' : 'https://rc.esewa.com.np/api/epay/transaction/status/';
    }

    private function signature(string $message): string
    {
        return base64_encode(hash_hmac('sha256', $message, $this->getCredential('secret_key', '8gBm/:&EnhH.1/q'), true));
    }

    public function purchase(Order $order, array $options = []): PaymentResponse
    {
        $productCode = $this->getCredential('product_code', $this->isLive() ? null : 'EPAYTEST');
        $secret = $this->getCredential('secret_key', $this->isLive() ? null : '8gBm/:&EnhH.1/q');
        if (! $productCode || ! $secret) {
            return PaymentResponse::make(false, 'failed', errorMessage: 'eSewa merchant credentials are not configured.');
        }
        $uuid = 'ORDER-'.$order->id.'-'.strtoupper(bin2hex(random_bytes(4)));
        $amount = number_format((float) $order->grand_total, 2, '.', '');
        $fields = [
            'amount' => $amount, 'tax_amount' => '0', 'total_amount' => $amount,
            'transaction_uuid' => $uuid, 'product_code' => $productCode,
            'product_service_charge' => '0', 'product_delivery_charge' => '0',
            'success_url' => route('payment.callback', ['gateway' => 'esewa']),
            'failure_url' => route('payment.cancel', ['order_id' => $order->id]),
            'signed_field_names' => 'total_amount,transaction_uuid,product_code',
        ];
        $fields['signature'] = $this->signature("total_amount={$amount},transaction_uuid={$uuid},product_code={$productCode}");
        session(['esewa_checkout' => ['action' => $this->formUrl(), 'fields' => $fields]]);

        return PaymentResponse::make(true, 'pending', $uuid, route('payment.esewa.checkout'));
    }

    public function verifyWebhook(Request $request): WebhookResult
    {
        $decoded = json_decode(base64_decode((string) $request->input('data'), true) ?: '', true);
        if (! is_array($decoded) || empty($decoded['signed_field_names']) || empty($decoded['signature'])) {
            return WebhookResult::make(false);
        }
        $message = collect(explode(',', $decoded['signed_field_names']))->map(fn ($field) => $field.'='.($decoded[$field] ?? ''))->implode(',');
        preg_match('/^ORDER-(\d+)-/', (string) ($decoded['transaction_uuid'] ?? ''), $matches);
        $orderId = $matches[1] ?? null;
        $order = $orderId ? Order::find($orderId) : null;
        $signatureValid = hash_equals($this->signature($message), (string) $decoded['signature']);
        $amountMatches = $order && abs((float) ($decoded['total_amount'] ?? -1) - (float) $order->grand_total) < 0.01;

        return WebhookResult::make($signatureValid && $amountMatches, $orderId, $decoded['transaction_code'] ?? $decoded['transaction_uuid'] ?? null, ($decoded['status'] ?? '') === 'COMPLETE' ? 'completed' : 'failed', isset($decoded['total_amount']) ? (float) $decoded['total_amount'] : null, 'callback', $decoded);
    }

    public function syncPaymentStatus(PaymentTransaction $transaction): PaymentResponse
    {
        $order = $transaction->order;
        $response = Http::get($this->statusUrl(), ['product_code' => $this->getCredential('product_code', 'EPAYTEST'), 'total_amount' => $order?->grand_total, 'transaction_uuid' => $transaction->gateway_transaction_id]);
        $data = $response->json() ?? [];

        return PaymentResponse::make($response->successful() && ($data['status'] ?? '') === 'COMPLETE', match ($data['status'] ?? '') {
            'COMPLETE' => 'completed', 'PENDING' => 'pending', default => 'failed'
        }, $data['ref_id'] ?? $transaction->gateway_transaction_id, rawPayload: $data);
    }

    public function refund(PaymentTransaction $transaction, float $amount, string $reason): RefundResponse
    {
        return RefundResponse::make(false, 'failed', errorMessage: 'Refunds must be processed from the eSewa merchant portal.');
    }

    public function checkHealth(): HealthResult
    {
        return HealthResult::make((bool) $this->getCredential('product_code', $this->isLive() ? null : 'EPAYTEST'), 'eSewa merchant configuration checked.');
    }

    public function getFormSchema(): array
    {
        return [
            Placeholder::make('esewa_documentation')
                ->label('eSewa ePay v2 credentials')
                ->content('Enter the merchant product/service code and HMAC-SHA256 secret key issued by eSewa. The success URL, failure URL, signed fields, and API endpoints are generated automatically.'),
            TextInput::make('credentials.product_code')
                ->label('Merchant ID / Product Code')
                ->required()->maxLength(255)
                ->helperText('The product_code issued by eSewa (EPAYTEST in the official sandbox).'),
            TextInput::make('credentials.secret_key')
                ->label('HMAC-SHA256 Secret Key')
                ->password()->revealable()->required()
                ->helperText('Used to sign total_amount, transaction_uuid, and product_code and to verify the signed callback.'),
        ];
    }

    public function getValidationRules(): array
    {
        return ['credentials.product_code' => ['required', 'string'], 'credentials.secret_key' => ['required', 'string']];
    }
}
