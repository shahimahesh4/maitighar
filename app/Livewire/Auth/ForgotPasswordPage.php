<?php

namespace App\Livewire\Auth;

use App\Traits\GoogleRecaptcha;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Forgot Password | Maitighar')]
class ForgotPasswordPage extends Component
{
    use GoogleRecaptcha;

    public string $email = '';

    public function sendResetLink(): void
    {
        $rules = array_merge($this->getRecaptchaRules(), [
            'email' => ['required', 'email'],
        ]);

        $this->validate($rules);

        if (! $this->verifyRecaptcha()) {
            return;
        }

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', __($status));

            return;
        }

        $this->addError('email', __($status));
    }

    public function render(): View
    {
        return view('livewire.auth.forgot-password-page');
    }
}
