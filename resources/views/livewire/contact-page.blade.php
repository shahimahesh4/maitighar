<div class="kba-contact-page kba-cms-page">
    <section class="kba-cms-hero" aria-labelledby="contact-page-title">
        <div class="kba-cms-hero-inner">
            <div class="kba-cms-hero-copy">
                <h1 id="contact-page-title">Contact Us</h1>
                <p>Questions about our products, orders, or custom requests? Our friendly team is ready to help.</p>
            </div>
        </div>
    </section>

    <section class="kba-contact-wrap">
        <div class="kba-contact-intro">
            <h2>Talk to our team</h2>
            <p>Choose the most convenient way to reach Maitighar.</p>

            <div class="kba-contact-cards">
                <a href="tel:{{ preg_replace('/[^+\d]/', '', $storePhone) }}" class="kba-contact-card">
                    <span class="kba-contact-card-icon"><i class="bi bi-telephone"></i></span>
                    <span><small>Call us</small><strong>{{ $storePhone }}</strong></span>
                </a>
                <a href="mailto:{{ $storeEmail }}" class="kba-contact-card">
                    <span class="kba-contact-card-icon"><i class="bi bi-envelope"></i></span>
                    <span><small>Email us</small><strong>{{ $storeEmail }}</strong></span>
                </a>
                <div class="kba-contact-card">
                    <span class="kba-contact-card-icon"><i class="bi bi-geo-alt"></i></span>
                    <span><small>Find us</small><strong>{{ $storeAddress }}</strong></span>
                </div>
            </div>

            <div class="kba-contact-meta">
                <div>
                    <span><i class="bi bi-clock"></i> Customer support</span>
                    <p>Monday – Saturday: 9:00 AM – 6:00 PM (AEST)<br>Sunday: Closed</p>
                </div>
                <div class="kba-contact-socials">
                    <a href="{{ \App\Models\Setting::get('facebook_url', 'https://www.facebook.com/maitigharaustralia') }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="{{ \App\Models\Setting::get('instagram_url', 'https://www.instagram.com/maitighar') }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="{{ \App\Models\Setting::get('youtube_url', 'https://www.youtube.com') }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                    <a href="{{ \App\Models\Setting::get('tiktok_url', 'https://www.tiktok.com') }}" target="_blank" rel="noopener" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
        </div>

        <div class="kba-contact-form-card" id="contact-form">
            <div class="kba-contact-form-heading">
                <span>We usually reply within one business day</span>
                <h2>Send us a message</h2>
                <p>Fill out the form and our team will get back to you as soon as possible.</p>
            </div>

            @if(session()->has('contact_success'))
                <div class="kba-contact-success" role="status">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('contact_success') }}</span>
                </div>
            @endif

            <form wire:submit.prevent="submit" class="kba-contact-form" data-kba-recaptcha-form>
                <div class="kba-contact-honeypot" aria-hidden="true">
                    <label for="website">Website</label>
                    <input type="text" id="website" wire:model="website" tabindex="-1" autocomplete="off">
                </div>
                <input type="hidden" id="recaptchaToken" wire:model="recaptchaToken">

                <div class="kba-contact-field kba-contact-field-full">
                    <label for="contact-name">Full name <b>*</b></label>
                    <div><i class="bi bi-person"></i><input type="text" id="contact-name" wire:model="full_name" autocomplete="name" placeholder="Enter your full name" required></div>
                    @error('full_name')<p>{{ $message }}</p>@enderror
                </div>

                <div class="kba-contact-field">
                    <label for="contact-email">Email address <b>*</b></label>
                    <div><i class="bi bi-envelope"></i><input type="email" id="contact-email" wire:model="email" autocomplete="email" placeholder="you@example.com" required></div>
                    @error('email')<p>{{ $message }}</p>@enderror
                </div>

                <div class="kba-contact-field">
                    <label for="contact-phone">Phone number <b>*</b></label>
                    <div class="kba-contact-phone-input"><span>🇦🇺 +61</span><input type="tel" id="contact-phone" wire:model="phone" autocomplete="tel" inputmode="tel" placeholder="0412 345 678" required></div>
                    @error('phone')<p>{{ $message }}</p>@enderror
                </div>

                <div class="kba-contact-field">
                    <label for="contact-address">Your location</label>
                    <div><i class="bi bi-geo-alt"></i><input type="text" id="contact-address" wire:model="address" autocomplete="street-address" placeholder="City or current location"></div>
                    @error('address')<p>{{ $message }}</p>@enderror
                </div>

                <div class="kba-contact-field">
                    <label for="contact-subject">Subject <b>*</b></label>
                    <div><i class="bi bi-bookmark"></i><input type="text" id="contact-subject" wire:model="subject" placeholder="How can we help?" required></div>
                    @error('subject')<p>{{ $message }}</p>@enderror
                </div>

                <div class="kba-contact-field kba-contact-field-full">
                    <label for="contact-message">Message <b>*</b></label>
                    <div class="kba-contact-textarea"><i class="bi bi-chat-left-text"></i><textarea id="contact-message" wire:model="message" rows="6" placeholder="Tell us about your question, enquiry, or product…" required></textarea></div>
                    <div class="kba-contact-message-meta"><span>Minimum 10 characters</span><span>We respect your privacy.</span></div>
                    @error('message')<p>{{ $message }}</p>@enderror
                </div>

                <div class="kba-contact-field">
                    @error('recaptchaToken')<p>{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="kba-contact-submit" wire:loading.attr="disabled" wire:target="submit">
                    <span wire:loading.remove wire:target="submit">Send message <i class="bi bi-send"></i></span>
                    <span wire:loading wire:target="submit"><i class="bi bi-arrow-repeat kba-spin"></i> Sending your message…</span>
                </button>
            </form>
        </div>
    </section>
</div>
