<?php

namespace App\Services;

use App\Mail\BrandedMessageMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OrderConfirmationOtpService
{
    private const EXPIRY_MINUTES = 10;
    private const RESEND_SECONDS = 60;
    private const MAX_ATTEMPTS = 5;

    public function send(User $user): void
    {
        if (Cache::has($this->cooldownKey($user))) {
            throw ValidationException::withMessages([
                'orderOtp' => 'Please wait one minute before requesting another code.',
            ]);
        }

        $code = (string) random_int(100000, 999999);
        Cache::put($this->codeKey($user), [
            'hash' => Hash::make($code),
            'attempts' => 0,
        ], now()->addMinutes(self::EXPIRY_MINUTES));
        Cache::put($this->cooldownKey($user), true, now()->addSeconds(self::RESEND_SECONDS));

        Mail::to($user->email)->send(new BrandedMessageMail(
            'Confirm your order',
            'Your order confirmation code',
            'Enter this one-time code at checkout to confirm your order and continue to payment.',
            ['Confirmation code' => $code, 'Expires in' => self::EXPIRY_MINUTES.' minutes'],
            messageText: 'Do not share this code. Maitighar will never ask for it by phone.',
        ));
    }

    public function pending(User $user): bool
    {
        return Cache::has($this->codeKey($user));
    }

    public function verify(User $user, string $code): bool
    {
        $payload = Cache::get($this->codeKey($user));
        if (! is_array($payload) || ($payload['attempts'] ?? self::MAX_ATTEMPTS) >= self::MAX_ATTEMPTS) {
            return false;
        }

        $payload['attempts']++;
        Cache::put($this->codeKey($user), $payload, now()->addMinutes(self::EXPIRY_MINUTES));

        if (! Hash::check($code, $payload['hash'] ?? '')) {
            return false;
        }

        Cache::forget($this->codeKey($user));
        Cache::forget($this->cooldownKey($user));

        return true;
    }

    private function codeKey(User $user): string
    {
        return 'order-confirmation-otp:'.$user->getKey();
    }

    private function cooldownKey(User $user): string
    {
        return $this->codeKey($user).':cooldown';
    }
}
