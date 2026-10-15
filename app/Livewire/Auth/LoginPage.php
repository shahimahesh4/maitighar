<?php

namespace App\Livewire\Auth;

use App\Traits\GoogleRecaptcha;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Sign In | Maitighar')]
class LoginPage extends Component
{
    use GoogleRecaptcha;

    public $email;

    public $password;

    public function save()
    {
        $rules = array_merge($this->getRecaptchaRules(), [
            'email' => 'required|email|max:255|exists:users,email',
            'password' => 'required|min:6',
        ]);

        $this->validate($rules);

        if (! $this->verifyRecaptcha()) {
            return;
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->flash('error', 'Invalid email or password.');

            return;
        }

        request()->session()->regenerate();

        if (in_array(Auth::user()->role, ['super_admin', 'admin', 'trainer', 'staff'], true)) {
            return redirect('/stnapanel');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function render(): View
    {
        return view('livewire.auth.login-page');
    }
}
