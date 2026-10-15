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

class KhaltiDriver extends AbstractGatewayDriver
{
    private function baseUrl(): string
    {
        return $this->isLive() ? 'https://khalti.com/api/v2' : 'https://dev.khalti.com/api/v2';
    }

    private function client()
    {
        return Http::acceptJson()->withHeaders(['Authorization' => 'Key '.$this->getCredential('secret_key', '')]);
    }

    public function purchase(Order $order, array $options = []): PaymentResponse
    {
        if (! $this->getCredential('secret_key')) {
            return PaymentResponse::make(false, 'failed', errorMessage: 'Khalti secret key is not configured.');
        }
        $response = $this->client()->post($this->baseUrl().'/epayment/initiate/', [
            'return_url' => route('payment.callback', ['gateway' => 'khalti']),
            'website_url' => $this->getSetting('website_url', config('app.url')),
            'amount' => (int) round($order->grand_total * 100),
            'purchase_order_id' => (string) $order->id,
            'purchase_order_name' => 'Order #'.$order->id,
            'merchant_name' => $this->getSetting('merchant_name'),
            'customer_info' => ['name' => $order->user?->name, 'email' => $order->user?->email, 'phone' => $order->address?->phone],
        ]);
        if (! $response->successful() || ! $response->json('pidx') || ! $response->json('payment_url')) {
            return PaymentResponse::make(false, 'failed', errorMessage: $response->json('detail') ?? 'Unable to initiate Khalti payment.', rawPayload: $response->json() ?? []);
        }

        return PaymentResponse::make(true, 'pending', $response->json('pidx'), $response->json('payment_url'), rawPayload: $response->json());
    }

    public function verifyWebhook(Request $request): WebhookResult
    {
        $pidx = $request->string('pidx')->toString();
        $orderId = $request->string('purchase_order_id')->toString();
        if (! $pidx || ! $orderId || ! $this->getCredential('secret_key')) {
            return WebhookResult::make(false, $orderId ?: null);
        }
        $response = $this->client()->post($this->baseUrl().'/epayment/lookup/', ['pidx' => $pidx]);
        $data = $response->json() ?? [];
        $order = Order::find($orderId);
        $amountMatches = $order && (int) ($data['total_amount'] ?? -1) === (int) round($order->grand_total * 100);
        $status = match ($data['status'] ?? '') {
            'Completed' => 'completed', 'Refunded' => 'refunded', 'Pending', 'Initiated' => 'pending', default => 'failed'
        };

        return WebhookResult::make($response->successful() && $amountMatches, $orderId, $data['transaction_id'] ?? $pidx, $status, isset($data['total_amount']) ? $data['total_amount'] / 100 : null, 'callback', $data);
    }

    public function syncPaymentStatus(PaymentTransaction $transaction): PaymentResponse
    {
        $request = Request::create('/', 'GET', ['pidx' => $transaction->gateway_transaction_id, 'purchase_order_id' => $transaction->order_id]);
        $result = $this->verifyWebhook($request);

        return PaymentResponse::make($result->valid, $result->status ?? 'failed', $result->gatewayTransactionId, rawPayload: $result->rawPayload);
    }

    public function refund(PaymentTransaction $transaction, float $amount, string $reason): RefundResponse
    {
        return RefundResponse::make(false, 'failed', errorMessage: 'Refunds must be processed from the Khalti merchant dashboard.');
    }

    public function checkHealth(): HealthResult
    {
        return HealthResult::make((bool) $this->getCredential('secret_key'), $this->getCredential('secret_key') ? 'Khalti credentials configured.' : 'Khalti secret key missing.');
    }

    public function getFormSchema(): array
    {
        return [
            Placeholder::make('khalti_documentation')
                ->label('Khalti KPG-2 credentials')
                ->content('Enter the live_secret_key from the Khalti merchant dashboard. Requests are sent with Authorization: Key <live_secret_key>. No separate merchant ID is required by KPG-2 Web Checkout.'),
            TextInput::make('credentials.secret_key')
                ->label('Authorization Key / Live Secret Key')
                ->password()->revealable()->required()
                ->helperText('Sandbox: test-admin.khalti.com. Production: admin.khalti.com. Store only the secret key value; the system adds the “Key” prefix.'),
            TextInput::make('settings.website_url')
                ->label('Merchant Website URL')
                ->url()->required()
                ->default(config('app.url'))
                ->helperText('Required by Khalti when initiating a payment.'),
            TextInput::make('settings.merchant_name')
                ->label('Merchant Display Name')
                ->maxLength(255)
                ->helperText('Optional merchant_name metadata returned by Khalti in the callback.'),
        ];
    }

    public function getValidationRules(): array
    {
        return ['credentials.secret_key' => ['required', 'string'], 'settings.website_url' => ['required', 'url']];
    }
}
