<div class="kba-auth-page">
    <div class="kba-login-shell" style="max-width: 920px;">
        <aside class="kba-login-story">
            <div class="kba-login-story-content">
                <img src="{{ asset('images/logo.png') }}" alt="Maitighar" class="kba-login-logo">
                <span class="kba-login-kicker">One last step</span>
                <h1>Verify your email address.</h1>
                <p>We sent a six-digit security code to {{ auth()->user()->email }}.</p>
            </div>
        </aside>

        <section class="kba-login-form-panel">
            <div class="kba-login-form-heading">
                <span>Email verification</span>
                <h2>Enter your email OTP</h2>
                <p>The code expires in 10 minutes and can only be used once.</p>
            </div>

            @if (session('status'))
                <div class="kba-auth-success">{{ session('status') }}</div>
            @endif

            <form wire:submit.prevent="verify" class="kba-login-form">
                <div class="kba-auth-field">
                    <label for="email-otp">Six-digit verification code</label>
                    <div class="kba-auth-input-wrap">
                        <i class="bi bi-envelope-check"></i>
                        <input id="email-otp" type="text" wire:model="otp" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="000000" required autofocus>
                    </div>
                    @error('otp') <p class="kba-auth-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="kba-auth-submit" wire:loading.attr="disabled" wire:target="verify">
                    <span wire:loading.remove wire:target="verify">Verify email <i class="bi bi-arrow-right"></i></span>
                    <span wire:loading wire:target="verify"><i class="bi bi-arrow-repeat kba-spin"></i> Verifying…</span>
                </button>
                <button type="button" wire:click="resend" class="kba-auth-secondary" wire:loading.attr="disabled" wire:target="resend">Send a new code</button>
            </form>
        </section>
    </div>
</div>
