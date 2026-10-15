<div class="kba-auth-page kba-recovery-page">
    <div class="kba-login-shell kba-recovery-shell">
        <aside class="kba-login-story kba-recovery-story">
            <div class="kba-login-story-content">
                <img src="{{ asset('images/logo.png') }}" alt="Maitighar" class="kba-login-logo">
                <span class="kba-login-kicker">Account recovery</span>
                <h1>We’ll help you get back in.</h1>
                <p>Enter the email connected to your account and we’ll send you a secure link to create a new password.</p>
                <div class="kba-login-benefits">
                    <span><i class="bi bi-shield-check"></i> Secure reset link</span>
                    <span><i class="bi bi-envelope-check"></i> Delivered to your email</span>
                    <span><i class="bi bi-clock"></i> Link expires automatically</span>
                </div>
            </div>
            <div class="kba-login-orbit kba-login-orbit-one"></div>
            <div class="kba-login-orbit kba-login-orbit-two"></div>
        </aside>

        <section class="kba-login-form-panel kba-recovery-form-panel">
            <div class="kba-login-form-heading">
                <span>Password assistance</span>
                <h2>Forgot your password?</h2>
                <p>Remembered it? <a wire:navigate href="{{ route('login') }}">Return to sign in</a></p>
            </div>

            @if (session()->has('status'))
                <div class="kba-auth-success" role="status">
                    <i class="bi bi-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form wire:submit.prevent="sendResetLink" class="kba-login-form" data-kba-recaptcha-form>
                <input type="hidden" id="recaptchaToken" wire:model="recaptchaToken">
                @error('recaptchaToken')<p class="kba-auth-error">{{ $message }}</p>@enderror
                <div class="kba-auth-field">
                    <label for="email">Email address</label>
                    <div class="kba-auth-input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" id="email" wire:model="email" autocomplete="email" placeholder="you@example.com" required autofocus>
                    </div>
                    <span class="kba-auth-field-hint">Use the email address registered with your account.</span>
                    @error('email')
                        <p class="kba-auth-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="kba-auth-submit" wire:loading.attr="disabled" wire:target="sendResetLink">
                    <span wire:loading.remove wire:target="sendResetLink">Send reset link <i class="bi bi-arrow-right"></i></span>
                    <span wire:loading wire:target="sendResetLink"><i class="bi bi-arrow-repeat kba-spin"></i> Sending link…</span>
                </button>
            </form>

            <a wire:navigate href="{{ route('login') }}" class="kba-recovery-back"><i class="bi bi-arrow-left"></i> Back to sign in</a>
        </section>
    </div>
</div>
