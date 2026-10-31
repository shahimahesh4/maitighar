<div class="kba-auth-page">
    <div class="kba-login-shell">
        <aside class="kba-login-story">
            <div class="kba-login-story-content">
                <img src="{{ asset('images/logo.webp') }}" alt="Maitighar" class="kba-login-logo">
                <span class="kba-login-kicker">Welcome to Maitighar</span>
                <h1>Nepalese Heritage with Love</h1>
                <p>Sign in to manage your orders, save favourites, and discover authentic Nepali collections selected by Maitighar.</p>
                <div class="kba-login-benefits">
                    <span><i class="bi bi-check2-circle"></i> Track every order</span>
                    <span><i class="bi bi-check2-circle"></i> Save your favourites</span>
                    <span><i class="bi bi-check2-circle"></i> Enjoy a faster checkout</span>
                </div>
            </div>
            <div class="kba-login-orbit kba-login-orbit-one"></div>
            <div class="kba-login-orbit kba-login-orbit-two"></div>
        </aside>

        <section class="kba-login-form-panel">
            <div class="kba-login-form-heading">
                <span>Welcome back</span>
                <h2>Sign in to your account</h2>
                <p>New to Maitighar? <a wire:navigate href="{{ route('register') }}">Create an account</a></p>
            </div>

            @if (session()->has('error'))
                <div class="kba-auth-alert" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form wire:submit.prevent="save" class="kba-login-form" data-kba-recaptcha-form>
                <input type="hidden" id="recaptchaToken" wire:model="recaptchaToken">
                @error('recaptchaToken')<p class="kba-auth-error">{{ $message }}</p>@enderror
                <div class="kba-auth-field">
                    <label for="email">Email address</label>
                    <div class="kba-auth-input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" id="email" wire:model="email" autocomplete="email" placeholder="you@example.com" required>
                    </div>
                    @error('email')
                        <p class="kba-auth-error" id="email-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="kba-auth-field">
                    <div class="kba-auth-label-row">
                        <label for="password">Password</label>
                        <a wire:navigate href="{{ route('forgot-password') }}">Forgot password?</a>
                    </div>
                    <div class="kba-auth-input-wrap kba-auth-password-wrap" x-data="{ showPassword: false }">
                        <i class="bi bi-lock"></i>
                        <input :type="showPassword ? 'text' : 'password'" id="password" wire:model="password" autocomplete="current-password" placeholder="Enter your password" required>
                        <button type="button" class="kba-password-toggle" @click="showPassword = ! showPassword" :aria-label="showPassword ? 'Hide password' : 'Show password'" :title="showPassword ? 'Hide password' : 'Show password'">
                            <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="kba-auth-error" id="password-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="kba-auth-submit" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Sign in <i class="bi bi-arrow-right"></i></span>
                    <span wire:loading wire:target="save"><i class="bi bi-arrow-repeat kba-spin"></i> Signing in…</span>
                </button>
            </form>

            <div class="kba-auth-secure-note">
                <i class="bi bi-shield-check"></i>
                <span>Your account is protected with secure authentication.</span>
            </div>
        </section>
    </div>
</div>
