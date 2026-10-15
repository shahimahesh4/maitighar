<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\CommunicationService;
use App\Services\EmailOtpService;
use App\Traits\GoogleRecaptcha;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create Account | Maitighar')]
class RegisterPage extends Component
{
    use GoogleRecaptcha;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function save()
    {
        $this->phone = $this->normalizePhone($this->phone);

        $rules = array_merge($this->getRecaptchaRules(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'regex:/^\+9779[78]\d{8}$/', 'unique:users,phone'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $validated = $this->validate($rules, [
            'phone.regex' => 'Enter a valid Nepal mobile number, for example +977 9812345678.',
            'password.required' => 'Create a strong password.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $communications = app(CommunicationService::class);

        if (! $this->verifyRecaptcha()) {
            return;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => $communications->emailVerificationEnabled() ? null : now(),
            'phone_verified_at' => $communications->phoneVerificationEnabled() ? null : now(),
        ]);

        if ($communications->receiptsEnabled()) {
            $communications->send($user->email, 'Welcome to Maitighar', 'Welcome to your Maitighar account', 'Your account registration was successful.', ['Name' => $user->name, 'Email' => $user->email, 'Phone' => $user->phone], 'Visit our store', url('/products'));
        }
        if ($communications->emailVerificationEnabled()) {
            app(EmailOtpService::class)->sendVerificationCode($user);
        }

        Auth::login($user);

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        if ($communications->emailVerificationEnabled()) {
            return redirect()->route('verification.notice');
        }

        if ($communications->phoneVerificationEnabled()) {
            return redirect()->route('phone.verification.notice');
        }

        return redirect()->intended('/');
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '977')) {
            $digits = substr($digits, 3);
        }

        return '+977'.$digits;
    }

    public function render(): View
    {
        return view('livewire.auth.register-page');
    }
}
