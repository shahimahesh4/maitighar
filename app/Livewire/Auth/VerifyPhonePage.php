<?php

namespace App\Livewire\Auth;

use App\Services\SparrowSmsService;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Verify Phone | Maitighar')]
class VerifyPhonePage extends Component
{
    public string $otp = '';

    public function mount(SparrowSmsService $sms): void
    {
        abort_unless(auth()->check(), 403);

        if (auth()->user()->phone_verified_at) {
            $this->redirect('/', navigate: true);

            return;
        }

        if (! auth()->user()->phone_verification_sent_at) {
            $sms->sendVerificationCode(auth()->user());
            session()->flash('status', 'A verification code was sent to your phone.');
        }
    }

    public function verify(SparrowSmsService $sms)
    {
        $validated = $this->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        if (! $sms->verify(auth()->user(), $validated['otp'])) {
            $this->addError('otp', 'The code is invalid, expired, or has exceeded the attempt limit.');

            return;
        }

        session()->flash('status', 'Your phone number has been verified.');

        return redirect()->intended('/');
    }

    public function resend(SparrowSmsService $sms): void
    {
        $sms->sendVerificationCode(auth()->user());
        $this->reset('otp');
        session()->flash('status', 'A new verification code was sent.');
    }

    public function render(): View
    {
        return view('livewire.auth.verify-phone-page');
    }
}
