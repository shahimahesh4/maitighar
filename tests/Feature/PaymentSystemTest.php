<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\Payment\Drivers\CodDriver;
use App\Services\Payment\Drivers\EsewaDriver;
use App\Services\Payment\Drivers\KhaltiDriver;
use App\Services\Payment\Drivers\ManualPaymentDriver;
use App\Services\Payment\ManualPaymentReviewService;
use App\Services\Payment\PaymentGatewayManager;
use Database\Seeders\PaymentGatewaySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        PaymentGateway::create(['name' => 'Cash on Delivery', 'code' => 'cod', 'driver' => 'cod', 'is_active' => true, 'environment' => 'sandbox', 'priority' => 10, 'is_default' => true, 'health_status' => 'healthy']);
        PaymentGateway::create(['name' => 'Khalti', 'code' => 'khalti', 'driver' => 'khalti', 'is_active' => true, 'environment' => 'sandbox', 'priority' => 1, 'is_default' => false, 'credentials' => ['secret_key' => 'test-secret'], 'health_status' => 'healthy']);
        PaymentGateway::create(['name' => 'eSewa', 'code' => 'esewa', 'driver' => 'esewa', 'is_active' => true, 'environment' => 'sandbox', 'priority' => 2, 'is_default' => false, 'credentials' => ['product_code' => 'EPAYTEST', 'secret_key' => '8gBm/:&EnhH.1/q'], 'health_status' => 'healthy']);
        PaymentGateway::create(['name' => 'Bank Transfer', 'code' => 'bank_transfer', 'driver' => 'manual', 'is_active' => true, 'environment' => 'live', 'priority' => 3, 'is_default' => false, 'health_status' => 'healthy']);
    }

    #[Test]
    public function manager_resolves_nepal_payment_drivers(): void
    {
        $manager = app(PaymentGatewayManager::class);
        $this->assertInstanceOf(KhaltiDriver::class, $manager->driver('khalti'));
        $this->assertInstanceOf(EsewaDriver::class, $manager->driver('esewa'));
        $this->assertInstanceOf(CodDriver::class, $manager->driver('cod'));
        $this->assertInstanceOf(ManualPaymentDriver::class, $manager->driver('bank_transfer'));
    }

    #[Test]
    public function cash_on_delivery_is_the_default_gateway(): void
    {
        $this->assertSame('cod', app(PaymentGatewayManager::class)->getDefaultGatewayCode());
    }

    #[Test]
    public function unavailable_online_gateway_falls_back_to_the_next_healthy_gateway(): void
    {
        $order = $this->order('khalti', 500);
        PaymentGateway::where('code', 'khalti')->update(['health_status' => 'down']);
        $response = app(PaymentGatewayManager::class)->fallbackRoute('khalti', $order);
        $this->assertTrue($response->success);
        $this->assertSame('esewa', $order->fresh()->payment_method);
    }

    #[Test]
    public function khalti_purchase_uses_npr_paisa_and_returns_checkout_url(): void
    {
        Http::fake(['https://dev.khalti.com/api/v2/epayment/initiate/' => Http::response(['pidx' => 'test-pidx', 'payment_url' => 'https://test-pay.khalti.com/?pidx=test-pidx'])]);
        $response = app(PaymentGatewayManager::class)->driver('khalti')->purchase($this->order('khalti', 1200));
        $this->assertTrue($response->success);
        $this->assertSame('test-pidx', $response->transactionId);
        $this->assertSame('https://test-pay.khalti.com/?pidx=test-pidx', $response->redirectUrl);
        Http::assertSent(fn ($request) => $request['amount'] === 120000 && $request['purchase_order_id'] !== null);
    }

    #[Test]
    public function esewa_purchase_builds_a_signed_checkout_request(): void
    {
        $response = app(PaymentGatewayManager::class)->driver('esewa')->purchase($this->order('esewa', 1500));
        $checkout = session('esewa_checkout');
        $this->assertTrue($response->success);
        $this->assertSame(route('payment.esewa.checkout'), $response->redirectUrl);
        $this->assertSame('1500.00', $checkout['fields']['total_amount']);
        $this->assertNotEmpty($checkout['fields']['signature']);
        $this->assertSame('EPAYTEST', $checkout['fields']['product_code']);
    }

    #[Test]
    public function manual_payment_waits_for_admin_verification(): void
    {
        $order = $this->order('bank_transfer', 2000);
        $response = app(PaymentGatewayManager::class)->driver('bank_transfer')->purchase($order);
        $this->assertTrue($response->success);
        $this->assertSame('pending_verification', $response->status);
    }

    #[Test]
    public function admin_can_approve_a_complete_manual_payment_proof(): void
    {
        Event::fake();
        $order = $this->order('bank_transfer', 2000);
        $admin = User::factory()->create();
        $transaction = PaymentTransaction::create([
            'order_id' => $order->id, 'user_id' => $order->user_id,
            'gateway_code' => 'bank_transfer', 'gateway_transaction_id' => 'MANUAL-TEST',
            'payment_reference' => 'BANK-REF-123', 'voucher_path' => 'payment-vouchers/test.png',
            'proof_submitted_at' => now(), 'amount' => 2000, 'currency' => 'NPR',
            'status' => 'pending_verification', 'type' => 'payment',
        ]);

        app(ManualPaymentReviewService::class)->approve($transaction, $admin->id);

        $this->assertSame('completed', $transaction->fresh()->status);
        $this->assertSame($admin->id, $transaction->fresh()->verified_by);
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame('processing', $order->fresh()->status);
    }

    #[Test]
    public function admin_can_reject_a_manual_payment_proof(): void
    {
        Event::fake();
        $order = $this->order('qr_payment', 900);
        $admin = User::factory()->create();
        $transaction = PaymentTransaction::create([
            'order_id' => $order->id, 'user_id' => $order->user_id,
            'gateway_code' => 'qr_payment', 'gateway_transaction_id' => 'MANUAL-QR',
            'payment_reference' => 'QR-REF-456', 'voucher_path' => 'payment-vouchers/qr.png',
            'proof_submitted_at' => now(), 'amount' => 900, 'currency' => 'NPR',
            'status' => 'pending_verification', 'type' => 'payment',
        ]);

        app(ManualPaymentReviewService::class)->reject($transaction, $admin->id, 'Voucher amount does not match.');

        $this->assertSame('rejected', $transaction->fresh()->status);
        $this->assertSame('Voucher amount does not match.', $transaction->fresh()->rejection_reason);
        $this->assertSame('failed', $order->fresh()->payment_status);
    }

    #[Test]
    public function official_gateway_credential_forms_expose_required_fields(): void
    {
        $khaltiFields = collect((new KhaltiDriver)->getFormSchema())->map(fn ($field) => $field->getName());
        $esewaFields = collect((new EsewaDriver)->getFormSchema())->map(fn ($field) => $field->getName());

        $this->assertTrue($khaltiFields->contains('credentials.secret_key'));
        $this->assertTrue($khaltiFields->contains('settings.website_url'));
        $this->assertTrue($esewaFields->contains('credentials.product_code'));
        $this->assertTrue($esewaFields->contains('credentials.secret_key'));
    }

    #[Test]
    public function gateway_seeding_preserves_existing_live_credentials(): void
    {
        $gateway = PaymentGateway::where('code', 'khalti')->firstOrFail();
        $gateway->update([
            'environment' => 'live',
            'is_active' => true,
            'credentials' => ['secret_key' => 'live_secret_do_not_overwrite'],
            'settings' => ['website_url' => 'https://shop.example.com'],
        ]);

        (new PaymentGatewaySeeder)->run();

        $gateway->refresh();
        $this->assertSame('live_secret_do_not_overwrite', $gateway->credentials['secret_key']);
        $this->assertSame('https://shop.example.com', $gateway->settings['website_url']);
        $this->assertSame('live', $gateway->environment);
        $this->assertTrue($gateway->is_active);
    }

    private function order(string $method, float $amount): Order
    {
        $user = User::factory()->create();

        return Order::create(['user_id' => $user->id, 'grand_total' => $amount, 'payment_method' => $method, 'payment_status' => 'pending', 'status' => 'new', 'currency' => 'NPR', 'shipping_amount' => 0, 'shipping_method' => 'standard']);
    }
}
