<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class SparrowSmsService
{
    public function enabled(): bool
    {
        return filter_var(Setting::get('phone_verification_enabled', false), FILTER_VALIDATE_BOOL)
            && filter_var(Setting::get('sparrow_sms_enabled', false), FILTER_VALIDATE_BOOL);
    }

    public function sendVerificationCode(User $user): void
    {
        if (! $this->enabled()) {
            throw ValidationException::withMessages(['otp' => 'Phone verification is currently unavailable.']);
        }

        if ($user->phone_verification_sent_at?->gt(now()->subSeconds($this->resendSeconds()))) {
            throw ValidationException::withMessages(['otp' => 'Please wait before requesting another verification code.']);
        }

        $code = (string) random_int(100000, 999999);
        $recipient = $this->localNumber($user->phone);
        $response = Http::asForm()
            ->timeout(15)
            ->post(Setting::get('sparrow_sms_endpoint', 'https://api.sparrowsms.com/v2/sms/'), [
                'token' => Setting::get('sparrow_sms_token'),
                'from' => Setting::get('sparrow_sms_identity'),
                'to' => $recipient,
                'text' => "Your Maitighar verification code is {$code}. It expires in {$this->expiryMinutes()} minutes. Do not share this code.",
            ]);

        $payload = $response->json();
        if (! $response->successful() || (int) ($payload['response_code'] ?? 0) !== 200) {
            report(new \RuntimeException('Sparrow SMS failed: '.($payload['response'] ?? $response->body())));
            throw ValidationException::withMessages(['otp' => 'We could not send the SMS. Please try again or contact Maitighar.']);
        }

        $user->forceFill([
            'phone_verification_code' => Hash::make($code),
            'phone_verification_expires_at' => now()->addMinutes($this->expiryMinutes()),
            'phone_verification_sent_at' => now(),
            'phone_verification_attempts' => 0,
        ])->save();
    }

    public function verify(User $user, string $code): bool
    {
        if ($user->phone_verified_at) {
            return true;
        }

        if (! $user->phone_verification_code || ! $user->phone_verification_expires_at?->isFuture()) {
            return false;
        }

        if ($user->phone_verification_attempts >= 5) {
            return false;
        }

        $user->increment('phone_verification_attempts');

        if (! Hash::check($code, $user->phone_verification_code)) {
            return false;
        }

        $user->forceFill([
            'phone_verified_at' => now(),
            'phone_verification_code' => null,
            'phone_verification_expires_at' => null,
            'phone_verification_attempts' => 0,
        ])->save();

        return true;
    }

    private function localNumber(string $phone): string
    {
        return substr(preg_replace('/\D+/', '', $phone), -10);
    }

    private function expiryMinutes(): int
    {
        return max(2, min(30, (int) Setting::get('phone_otp_expiry_minutes', 10)));
    }

    private function resendSeconds(): int
    {
        return max(30, min(300, (int) Setting::get('phone_otp_resend_seconds', 60)));
    }
}
