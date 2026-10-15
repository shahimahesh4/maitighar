<footer class="kba-footer" id="contact">
    <div class="kba-footer-trust" aria-label="Customer benefits">
        <div class="kba-footer-trust-grid">
            @foreach($trustItems as $item)
                <div><span><i class="bi {{ $item['icon'] }}"></i></span><p><strong>{{ $item['title'] }}</strong><small>{{ $item['text'] }}</small></p></div>
            @endforeach
        </div>
    </div>

    <div class="kba-footer-main">
        <div class="kba-footer-grid">
            <section class="kba-footer-brand">
                <a wire:navigate href="{{ route('index') }}" class="kba-footer-logo" aria-label="Maitighar home">
                    <span><strong>Maitighar</strong> <small>Nepalese Heritage with Love</small></span>
                </a>
                <p>Nepalese heritage, culture, and craftsmanship — celebrating authentic tradition and timeless heritage with love.</p>
                <a wire:navigate href="{{ route('contact') }}" class="kba-footer-cta">Talk to our team <i class="bi bi-arrow-up-right"></i></a>
            </section>

            <section class="kba-footer-links">
                <span class="kba-footer-label">Explore</span>
                <h3>Discover</h3>
                <nav aria-label="Footer explore navigation">
                    @foreach($exploreLinks as $link)
                        <a wire:navigate href="{{ $link['url'] }}">{{ $link['label'] }}</a>
                    @endforeach
                </nav>
            </section>

            <section class="kba-footer-links">
                <span class="kba-footer-label">Support</span>
                <h3>Helpful links</h3>
                <nav aria-label="Footer support navigation">
                    @foreach($supportLinks as $link)
                        <a wire:navigate href="{{ $link['url'] }}">{{ $link['label'] }}</a>
                    @endforeach
                </nav>
            </section>

            <section class="kba-footer-contact">
                <span class="kba-footer-label">Get in touch</span>
                <h3>Contact details</h3>
                <div class="kba-footer-contact-list">
                    @foreach($contactItems as $item)
                        @if(isset($item['url']))
                            <a href="{{ $item['url'] }}"><i class="bi {{ $item['icon'] }}"></i><span>{{ $item['value'] }}</span></a>
                        @else
                            <div><i class="bi {{ $item['icon'] }}"></i><span>{{ $item['value'] }}</span></div>
                        @endif
                    @endforeach
                </div>
            </section>
        </div>

        <div class="kba-footer-newsletter">
            <div><span><i class="bi bi-envelope-heart"></i></span><p><strong>Maitighar updates & stories</strong><small>Occasional news, new arrivals, and special cultural offers—delivered with care.</small></p></div>
            <form wire:submit="subscribe">
                <label for="footer-email" class="sr-only">Email address</label>
                <input id="footer-email" type="email" wire:model="subscriberEmail" placeholder="Enter your email address" autocomplete="email" required>
                <button type="submit" wire:loading.attr="disabled" wire:target="subscribe"><span wire:loading.remove wire:target="subscribe">Subscribe <i class="bi bi-arrow-right"></i></span><span wire:loading wire:target="subscribe">Saving…</span></button>
            </form>
            @error('subscriberEmail')<p class="kba-footer-form-message is-error">{{ $message }}</p>@enderror
            @if(session()->has('footer_subscribed'))<p class="kba-footer-form-message" role="status"><i class="bi bi-check-circle"></i> {{ session('footer_subscribed') }}</p>@endif
        </div>
    </div>

    <div class="kba-footer-bottom">
        <div class="kba-footer-bottom-grid">
            <div class="kba-copyright">
                <p>© {{ now()->year }} Maitighar. All rights reserved.</p>
                <p>Crafted by: <a href="https://siddhitechnepal.com" target="_blank" rel="noopener">Siddhi Tech Nepal</a></p>
            </div>
            <div class="kba-payment-methods">
                <span>Secure payment options</span>
                <div class="kba-payment-badges" aria-label="Accepted payment methods: Cash on Delivery, Visa, Mastercard, PayPal">
                    <span class="kba-pay-badge kba-pay-cod" title="Cash on Delivery">
                        <i class="bi bi-cash-stack"></i>
                        <small>COD</small>
                    </span>
                    <span class="kba-pay-badge" title="Visa">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 32" class="kba-pay-svg">
                            <path fill="#1A1F71" d="M19.16 20.89l2.42-12h3.87l-2.42 12h-3.87zm14.8-11.75c-.77-.3-1.97-.62-3.46-.62-3.81 0-6.5 1.98-6.52 4.82-.03 2.1 1.92 3.26 3.39 3.96 1.51.72 2.02 1.18 2.01 1.83-.02.99-1.22 1.44-2.35 1.44-1.57 0-2.41-.23-3.7-.79l-.51-.25-.56 3.38c.93.42 2.65.79 4.43.81 4.18 0 6.9-2.02 6.94-5.14.02-1.71-1.05-3.01-3.34-4.09-1.39-.7-2.24-1.17-2.23-1.88 0-.63.72-1.3 2.29-1.3 1.31-.02 2.26.27 2.99.58l.36.17.55-3.33zm9.05 0h-3c-.93 0-1.63.26-2.04 1.21l-5.78 13.54h4.07s.67-1.81.82-2.21c.45 0 4.4.01 4.97.01.12.51.48 2.2 4.97 2.2h3.6l-3.63-14.75zm-4.85 9.42c.33-.87 1.58-4.17 1.58-4.17-.02.04.32-.86.52-1.42l.27 1.29s.75 3.56.91 4.3h-3.28zm-19.4-9.42l-3.81 10.24-.41-2.01c-.71-2.35-2.92-4.9-5.4-6.18l3.49 12.94 4.09-.01 6.09-14.98h-4.05z"/>
                            <path fill="#F7B600" d="M8.28 9.14H2.03L2 9.29c4.83 1.21 8.03 4.14 9.36 7.64l-1.35-6.68c-.23-.88-.88-1.08-1.73-1.11z"/>
                        </svg>
                    </span>
                    <span class="kba-pay-badge" title="Mastercard">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 38 24" class="kba-pay-svg">
                            <circle cx="14" cy="12" r="9" fill="#EB001B"/>
                            <circle cx="24" cy="12" r="9" fill="#F79E1B"/>
                            <path d="M19 5.86a8.97 8.97 0 0 0-3 6.14 8.97 8.97 0 0 0 3 6.14 8.97 8.97 0 0 0 3-6.14 8.97 8.97 0 0 0-3-6.14z" fill="#FF5F00"/>
                        </svg>
                    </span>
                    <span class="kba-pay-badge" title="PayPal">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 38 24" class="kba-pay-svg">
                            <path fill="#003087" d="M14.5 4h5.2c2.7 0 4.6 1.4 4.2 4.1-.4 2.9-2.5 4.5-5.2 4.5h-2.1l-1.3 7.4h-3.3l2.5-16z"/>
                            <path fill="#0079C1" d="M17.5 7h4.8c2.4 0 4.1 1.2 3.8 3.6-.4 2.6-2.2 4-4.6 4h-1.9l-1.2 6.4h-2.9l2-14z"/>
                            <path fill="#002169" opacity=".4" d="M18.7 14.6l.8-4.6h2.8c2.4 0 3.7 1.2 3.4 3.4-.3 1.8-1.5 2.9-3.3 3.1l-1.8.1h-1.9z"/>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="kba-socials"><span>Follow our journey</span><div>@foreach($socialLinks as $link)<a href="{{ $link['url'] }}" target="_blank" rel="noopener" aria-label="{{ $link['label'] }}"><i class="bi {{ $link['icon'] }}"></i></a>@endforeach</div></div>
        </div>
    </div>
</footer>
