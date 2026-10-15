<div class="kba-auth-page kba-recovery-page">
    <div class="kba-login-shell kba-recovery-shell">
        <aside class="kba-login-story kba-recovery-story">
            <div class="kba-login-story-content">
                <img src="{{ asset('images/logo.png') }}" alt="Maitighar" class="kba-login-logo">
                <span class="kba-login-kicker">Create a new password</span>
                <h1>Fresh start, same account.</h1>
                <p>Choose a secure new password to restore access to your orders, wishlist, and account details.</p>
                <div class="kba-login-benefits">
                    <span><i class="bi bi-check2-circle"></i> Use at least 8 characters</span>
                    <span><i class="bi bi-check2-circle"></i> Keep your password private</span>
                    <span><i class="bi bi-check2-circle"></i> Sign in after resetting</span>
                </div>
            </div>
            <div class="kba-login-orbit kba-login-orbit-one"></div>
            <div class="kba-login-orbit kba-login-orbit-two"></div>
        </aside>

        <section class="kba-login-form-panel kba-recovery-form-panel">
            <div class="kba-login-form-heading">
                <span>Secure your account</span>
                <h2>Reset your password</h2>
                <p>Enter your email and choose a new password below.</p>
            </div>

            <form wire:submit.prevent="save" class="kba-login-form kba-reset-form" data-kba-recaptcha-form>
                <input type="hidden" id="recaptchaToken" wire:model="recaptchaToken">
                @error('recaptchaToken')<p class="kba-auth-error">{{ $message }}</p>@enderror
                <div class="kba-auth-field">
                    <label for="email">Email address</label>
                    <div class="kba-auth-input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" id="email" wire:model="email" autocomplete="email" placeholder="you@example.com" required>
                    </div>
                    @error('email')<p class="kba-auth-error">{{ $message }}</p>@enderror
                </div>

                <div class="kba-auth-field">
                    <label for="password">New password</label>
                    <div class="kba-auth-input-wrap kba-auth-password-wrap" x-data="{ showPassword: false }">
                        <i class="bi bi-lock"></i>
                        <input :type="showPassword ? 'text' : 'password'" id="password" wire:model="password" autocomplete="new-password" placeholder="At least 8 characters" required>
                        <button type="button" class="kba-password-toggle" @click="showPassword = ! showPassword" :aria-label="showPassword ? 'Hide password' : 'Show password'" :title="showPassword ? 'Hide password' : 'Show password'">
                            <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                    </div>
                    @error('password')<p class="kba-auth-error">{{ $message }}</p>@enderror
                </div>

                <div class="kba-auth-field">
                    <label for="password_confirmation">Confirm new password</label>
                    <div class="kba-auth-input-wrap kba-auth-password-wrap" x-data="{ showPassword: false }">
                        <i class="bi bi-shield-lock"></i>
                        <input :type="showPassword ? 'text' : 'password'" id="password_confirmation" wire:model="password_confirmation" autocomplete="new-password" placeholder="Repeat your new password" required>
                        <button type="button" class="kba-password-toggle" @click="showPassword = ! showPassword" :aria-label="showPassword ? 'Hide password' : 'Show password'" :title="showPassword ? 'Hide password' : 'Show password'">
                            <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="kba-auth-submit" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Save new password <i class="bi bi-arrow-right"></i></span>
                    <span wire:loading wire:target="save"><i class="bi bi-arrow-repeat kba-spin"></i> Updating…</span>
                </button>
            </form>
        </section>
    </div>
</div>
