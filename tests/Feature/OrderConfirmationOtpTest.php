<?php

namespace Tests\Feature;

use App\Mail\BrandedMessageMail;
use App\Models\User;
use App\Services\OrderConfirmationOtpService;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderConfirmationOtpTest extends TestCase
{
    public function test_it_sends_and_verifies_an_order_confirmation_code(): void
    {
        Mail::fake();
        config(['cache.default' => 'array']);
        $user = $this->customer();
        $service = app(OrderConfirmationOtpService::class);

        $service->send($user);

        $code = null;
        Mail::assertSent(BrandedMessageMail::class, function (BrandedMessageMail $mail) use (&$code, $user): bool {
            $code = $mail->details['Confirmation code'] ?? null;

            return $mail->hasTo($user->email) && is_string($code);
        });

        $this->assertTrue($service->pending($user));
        $this->assertTrue($service->verify($user, $code));
        $this->assertFalse($service->pending($user));
    }

    public function test_it_rejects_an_invalid_order_confirmation_code(): void
    {
        Mail::fake();
        config(['cache.default' => 'array']);
        $user = $this->customer();
        $service = app(OrderConfirmationOtpService::class);

        $service->send($user);

        $this->assertFalse($service->verify($user, '000000'));
        $this->assertTrue($service->pending($user));
    }

    private function customer(): User
    {
        $user = new User(['name' => 'OTP Customer', 'email' => 'customer@example.com']);
        $user->id = 123;
        $user->exists = true;

        return $user;
    }
}
