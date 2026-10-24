<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentVoucherAuthorizationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_only_super_admin_and_admin_can_view_private_payment_receipts(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('payment-vouchers/receipt.jpg', 'private receipt');
        $transaction = $this->transaction('payment-vouchers/receipt.jpg');

        foreach (['super_admin', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get(route('payment-vouchers.show', $transaction))->assertOk();
        }

        foreach (['customer', 'student', 'trainer', 'staff'] as $role) {
            $user = User::factory()->create(['role' => $role, 'admin_permissions' => ['payments', 'orders']]);
            $this->actingAs($user)->get(route('payment-vouchers.show', $transaction))->assertForbidden();
        }
    }

    public function test_guests_cannot_view_payment_receipts(): void
    {
        $transaction = $this->transaction('payment-vouchers/receipt.jpg');

        $this->get(route('payment-vouchers.show', $transaction))->assertRedirect(route('login'));
    }

    public function test_public_storage_is_never_used_as_a_receipt_fallback(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Storage::disk('public')->put('payment-vouchers/public.jpg', 'public receipt');
        $transaction = $this->transaction('payment-vouchers/public.jpg');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('payment-vouchers.show', $transaction))->assertNotFound();
    }

    private function transaction(string $path): PaymentTransaction
    {
        $user = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->create(['user_id' => $user->id]);

        return PaymentTransaction::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'gateway_code' => 'bank_transfer',
            'amount' => $order->grand_total,
            'currency' => 'NPR',
            'status' => 'pending_verification',
            'type' => 'payment',
            'voucher_path' => $path,
        ]);
    }
}
