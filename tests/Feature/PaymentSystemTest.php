<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\Payment\Drivers\CodDriver;
use App\Services\Payment\Drivers\ManualPaymentDriver;
use App\Services\Payment\ManualPaymentReviewService;
use App\Services\Payment\PaymentGatewayManager;
use Database\Seeders\PaymentGatewaySeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentSystemTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        PaymentGateway::create([
            'name' => 'Cash on Delivery',
            'code' => 'cod',
            'driver' => 'cod',
            'is_active' => true,
            'environment' => 'live',
            'priority' => 2,
            'is_default' => true,
            'health_status' => 'healthy',
        ]);

        PaymentGateway::create([
            'name' => 'Bank Transfer',
            'code' => 'bank_transfer',
            'driver' => 'manual',
            'is_active' => true,
            'environment' => 'live',
            'priority' => 1,
            'is_default' => false,
            'health_status' => 'healthy',
        ]);
    }

    #[Test]
    public function manager_resolves_payment_drivers(): void
    {
        $manager = app(PaymentGatewayManager::class);
        $this->assertInstanceOf(CodDriver::class, $manager->driver('cod'));
        $this->assertInstanceOf(ManualPaymentDriver::class, $manager->driver('bank_transfer'));
    }

    #[Test]
    public function cash_on_delivery_is_the_default_gateway(): void
    {
        $this->assertSame('cod', app(PaymentGatewayManager::class)->getDefaultGatewayCode());
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
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'gateway_code' => 'bank_transfer',
            'gateway_transaction_id' => 'MANUAL-TEST',
            'payment_reference' => 'BANK-REF-123',
            'voucher_path' => 'payment-vouchers/test.png',
            'proof_submitted_at' => now(),
            'amount' => 2000,
            'currency' => 'NPR',
            'status' => 'pending_verification',
            'type' => 'payment',
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
        $order = $this->order('bank_transfer', 900);
        $admin = User::factory()->create();
        $transaction = PaymentTransaction::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'gateway_code' => 'bank_transfer',
            'gateway_transaction_id' => 'MANUAL-TEST-2',
            'payment_reference' => 'BANK-REF-456',
            'voucher_path' => 'payment-vouchers/test.png',
            'proof_submitted_at' => now(),
            'amount' => 900,
            'currency' => 'NPR',
            'status' => 'pending_verification',
            'type' => 'payment',
        ]);

        app(ManualPaymentReviewService::class)->reject($transaction, $admin->id, 'Voucher amount does not match.');

        $this->assertSame('rejected', $transaction->fresh()->status);
        $this->assertSame('Voucher amount does not match.', $transaction->fresh()->rejection_reason);
        $this->assertSame('failed', $order->fresh()->payment_status);
    }

    #[Test]
    public function payment_gateway_seeder_only_seeds_supported_gateways(): void
    {
        (new PaymentGatewaySeeder)->run();

        $codes = PaymentGateway::pluck('code')->all();
        $this->assertContains('cod', $codes);
        $this->assertContains('bank_transfer', $codes);
        $this->assertNotContains('khalti', $codes);
        $this->assertNotContains('esewa', $codes);
        $this->assertNotContains('qr_payment', $codes);
        $this->assertNotContains('fonepay', $codes);
    }

    private function order(string $method, float $amount): Order
    {
        $user = User::factory()->create();

        return Order::create([
            'user_id' => $user->id,
            'grand_total' => $amount,
            'payment_method' => $method,
            'payment_status' => 'pending',
            'status' => 'new',
            'currency' => 'NPR',
            'shipping_amount' => 0,
            'shipping_method' => 'standard',
        ]);
    }
}
