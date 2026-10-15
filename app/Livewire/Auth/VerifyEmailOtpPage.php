<?php

namespace App\Livewire\Auth;

use App\Services\EmailOtpService;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Verify Email | Maitighar')]
class VerifyEmailOtpPage extends Component
{
    public string $otp = '';

    public function mount(EmailOtpService $otpService): void
    {
        abort_unless(auth()->check(), 403);

        if (auth()->user()->hasVerifiedEmail()) {
            $this->redirectRoute('dashboard', navigate: true);
            return;
        }

        if (! auth()->user()->email_verification_sent_at) {
            $otpService->sendVerificationCode(auth()->user());
            session()->flash('status', 'A verification code was sent to your email address.');
        }
    }

    public function verify(EmailOtpService $otpService)
    {
        $validated = $this->validate(['otp' => ['required', 'digits:6']]);

        if (! $otpService->verify(auth()->user(), $validated['otp'])) {
            $this->addError('otp', 'The code is invalid, expired, or has exceeded the attempt limit.');
            return null;
        }

        session()->flash('livewire-alert', [
            'type' => 'success',
            'title' => 'Congratulations!',
            'message' => 'Your email has been verified and your account is ready.',
            'timer' => 5000,
        ]);

        return redirect()->route('dashboard');
    }

    public function resend(EmailOtpService $otpService): void
    {
        $otpService->sendVerificationCode(auth()->user());
        $this->reset('otp');
        session()->flash('status', 'A new verification code was sent to your email address.');
    }

    public function render(): View
    {
        return view('livewire.auth.verify-email-otp-page');
    }
}
