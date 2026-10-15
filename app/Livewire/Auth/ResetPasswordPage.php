<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Traits\GoogleRecaptcha;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Reset Password | Maitighar')]
class ResetPasswordPage extends Component
{
    use GoogleRecaptcha;

    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = (string) request()->query('email', '');
    }

    public function save()
    {
        $rules = array_merge($this->getRecaptchaRules(), [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $validated = $this->validate($rules);

        if (! $this->verifyRecaptcha()) {
            return;
        }

        $status = Password::reset(
            $validated,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', __($status));

            return redirect()->route('login');
        }

        $this->addError('email', __($status));
    }

    public function render(): View
    {
        return view('livewire.auth.reset-password-page');
    }
}
