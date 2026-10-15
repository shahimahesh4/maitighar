<div class="kba-auth-page kba-register-page">
    <div class="kba-login-shell kba-register-shell">
        <aside class="kba-login-story kba-register-story">
            <div class="kba-login-story-content">
                <img src="{{ asset('images/logo.png') }}" alt="Maitighar" class="kba-login-logo">
                <span class="kba-login-kicker">Welcome to Maitighar</span>
                <h1>Nepalese Heritage with Love</h1>
                <p>Create your account for a personalized shopping experience and a special place for your favourite heritage items and services.</p>
                <div class="kba-login-benefits">
                    <span><i class="bi bi-gem"></i> Curated authentic collections</span>
                    <span><i class="bi bi-heart"></i> A personal wishlist</span>
                    <span><i class="bi bi-box-seam"></i> Easy orders and tracking</span>
                </div>
            </div>
            <div class="kba-login-orbit kba-login-orbit-one"></div>
            <div class="kba-login-orbit kba-login-orbit-two"></div>
        </aside>

        <section class="kba-login-form-panel kba-register-form-panel">
            <div class="kba-login-form-heading">
                <span>Become a member</span>
                <h2>Create your account</h2>
                <p>Already registered? <a wire:navigate href="{{ route('login') }}">Sign in instead</a></p>
            </div>

            <form wire:submit.prevent="save" class="kba-login-form kba-register-form" data-kba-recaptcha-form>
                <input type="hidden" id="recaptchaToken" wire:model="recaptchaToken">
                @error('recaptchaToken')<p class="kba-auth-error">{{ $message }}</p>@enderror
                <div class="kba-auth-field">
                    <label for="name">Full name</label>
                    <div class="kba-auth-input-wrap">
                        <i class="bi bi-person"></i>
                        <input type="text" id="name" wire:model="name" autocomplete="name" placeholder="Your full name" required autofocus>
                    </div>
                    @error('name')
                        <p class="kba-auth-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="kba-auth-field">
                    <label for="email">Email address</label>
                    <div class="kba-auth-input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input type="email" id="email" wire:model="email" autocomplete="email" placeholder="you@example.com" required>
                    </div>
                    @error('email')
                        <p class="kba-auth-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="kba-auth-field">
                    <label for="phone">Phone number</label>
                    <div class="kba-auth-input-wrap">
                        <i class="bi bi-phone"></i>
                        <input type="tel" id="phone" wire:model="phone" autocomplete="tel" inputmode="tel" placeholder="+977 9812345678" required>
                    </div>
                    <span class="kba-auth-field-hint">Nepal mobile number beginning with 97 or 98.</span>
                    @error('phone')
                        <p class="kba-auth-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="kba-register-password-grid">
                    <div class="kba-auth-field">
                        <label for="password">Password</label>
                        <div class="kba-auth-input-wrap kba-auth-password-wrap" x-data="{ showPassword: false }">
                            <i class="bi bi-lock"></i>
                            <input :type="showPassword ? 'text' : 'password'" id="password" wire:model="password" autocomplete="new-password" placeholder="Create a strong password" required>
                            <button type="button" class="kba-password-toggle" @click="showPassword = ! showPassword" :aria-label="showPassword ? 'Hide password' : 'Show password'" :title="showPassword ? 'Hide password' : 'Show password'">
                                <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="kba-auth-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="kba-auth-field">
                        <label for="password_confirmation">Confirm password</label>
                        <div class="kba-auth-input-wrap kba-auth-password-wrap" x-data="{ showPassword: false }">
                            <i class="bi bi-shield-lock"></i>
                            <input :type="showPassword ? 'text' : 'password'" id="password_confirmation" wire:model="password_confirmation" autocomplete="new-password" placeholder="Repeat password" required>
                            <button type="button" class="kba-password-toggle" @click="showPassword = ! showPassword" :aria-label="showPassword ? 'Hide password' : 'Show password'" :title="showPassword ? 'Hide password' : 'Show password'">
                                <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <label class="kba-register-terms">
                    <input type="checkbox" required>
                    <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</span>
                </label>

                <button type="submit" class="kba-auth-submit" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Create account <i class="bi bi-arrow-right"></i></span>
                    <span wire:loading wire:target="save"><i class="bi bi-arrow-repeat kba-spin"></i> Creating account…</span>
                </button>
            </form>

            <div class="kba-auth-secure-note">
                <i class="bi bi-shield-check"></i>
                <span>Your personal information stays private and protected.</span>
            </div>
        </section>
    </div>
</div>
