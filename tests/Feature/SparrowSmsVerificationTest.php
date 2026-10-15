<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Services\SparrowSmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SparrowSmsVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sparrow_sends_and_stores_a_hashed_otp(): void
    {
        $this->enableSparrow();
        Http::fake([
            'api.sparrowsms.com/*' => Http::response([
                'count' => 1,
                'response_code' => 200,
                'response' => '1 messages has been queued for delivery',
            ]),
        ]);

        $user = User::factory()->create([
            'phone' => '+9779812345678',
            'phone_verified_at' => null,
        ]);

        app(SparrowSmsService::class)->sendVerificationCode($user);
        $user->refresh();

        $this->assertNotNull($user->phone_verification_code);
        $this->assertNotNull($user->phone_verification_expires_at);
        $this->assertFalse(Hash::check('000000', $user->phone_verification_code));

        Http::assertSent(fn ($request) => $request['to'] === '9812345678'
            && str_contains($request['text'], 'Maitighar')
        );
    }

    public function test_valid_otp_verifies_phone_and_clears_secret(): void
    {
        $user = User::factory()->create([
            'phone' => '+9779812345678',
            'phone_verified_at' => null,
            'phone_verification_code' => Hash::make('123456'),
            'phone_verification_expires_at' => now()->addMinutes(10),
        ]);

        $this->assertTrue(app(SparrowSmsService::class)->verify($user, '123456'));
        $user->refresh();

        $this->assertNotNull($user->phone_verified_at);
        $this->assertNull($user->phone_verification_code);
    }

    public function test_invalid_otp_does_not_verify_phone(): void
    {
        $user = User::factory()->create([
            'phone' => '+9779812345678',
            'phone_verified_at' => null,
            'phone_verification_code' => Hash::make('123456'),
            'phone_verification_expires_at' => now()->addMinutes(10),
        ]);

        $this->assertFalse(app(SparrowSmsService::class)->verify($user, '654321'));
        $this->assertNull($user->fresh()->phone_verified_at);
    }

    private function enableSparrow(): void
    {
        Setting::set('phone_verification_enabled', true);
        Setting::set('sparrow_sms_enabled', true);
        Setting::set('sparrow_sms_token', 'test-token');
        Setting::set('sparrow_sms_identity', 'KBA');
        Setting::set('sparrow_sms_endpoint', 'https://api.sparrowsms.com/v2/sms/');
    }
}
