<div class="kba-auth-page">
    <div class="kba-login-shell" style="max-width: 920px;">
        <aside class="kba-login-story">
            <div class="kba-login-story-content">
                <img src="{{ asset('images/logo.webp') }}" alt="Maitighar" class="kba-login-logo">
                <span class="kba-login-kicker">Secure your account</span>
                <h1>Verify your phone number.</h1>
                <p>We sent a six-digit security code to {{ auth()->user()->phone }} through Sparrow SMS.</p>
            </div>
        </aside>

        <section class="kba-login-form-panel">
            <div class="kba-login-form-heading">
                <span>Phone verification</span>
                <h2>Enter your SMS code</h2>
                <p>The code expires shortly and can only be used once.</p>
            </div>

            @if (session('status'))
                <div class="kba-auth-success">{{ session('status') }}</div>
            @endif

            <form wire:submit.prevent="verify" class="kba-login-form">
                <div class="kba-auth-field">
                    <label for="otp">Six-digit verification code</label>
                    <div class="kba-auth-input-wrap">
                        <i class="bi bi-shield-lock"></i>
                        <input id="otp" type="text" wire:model="otp" inputmode="numeric" autocomplete="one-time-code"
                            maxlength="6" placeholder="000000" required autofocus>
                    </div>
                    @error('otp') <p class="kba-auth-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="kba-auth-submit" wire:loading.attr="disabled">
                    Verify phone <i class="bi bi-arrow-right"></i>
                </button>
                <button type="button" wire:click="resend" class="kba-auth-secondary" wire:loading.attr="disabled">
                    Send a new code
                </button>
            </form>
        </section>
    </div>
</div>
