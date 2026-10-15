<?php

namespace App\Services;

use App\Mail\BrandedMessageMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class EmailOtpService
{
    private const EXPIRY_MINUTES = 10;
    private const RESEND_SECONDS = 60;
    private const MAX_ATTEMPTS = 5;

    public function sendVerificationCode(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        if ($user->email_verification_sent_at?->gt(now()->subSeconds(self::RESEND_SECONDS))) {
            throw ValidationException::withMessages(['otp' => 'Please wait before requesting another verification code.']);
        }

        $code = (string) random_int(100000, 999999);
        $user->forceFill([
            'email_verification_code' => Hash::make($code),
            'email_verification_expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
            'email_verification_sent_at' => now(),
            'email_verification_attempts' => 0,
        ])->save();

        Mail::to($user->email)->send(new BrandedMessageMail(
            'Your email verification code',
            'Verify your email address',
            'Enter this one-time code to finish creating your Maitighar account.',
            ['Verification code' => $code, 'Expires in' => self::EXPIRY_MINUTES.' minutes'],
            messageText: 'For your security, do not share this code with anyone.',
        ));
    }

    public function verify(User $user, string $code): bool
    {
        if ($user->hasVerifiedEmail()) {
            return true;
        }

        if (! $user->email_verification_code || ! $user->email_verification_expires_at?->isFuture() || $user->email_verification_attempts >= self::MAX_ATTEMPTS) {
            return false;
        }

        $user->increment('email_verification_attempts');
        if (! Hash::check($code, $user->email_verification_code)) {
            return false;
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_expires_at' => null,
            'email_verification_sent_at' => null,
            'email_verification_attempts' => 0,
        ])->save();

        return true;
    }
}
