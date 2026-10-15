@php
    $pageDesign = match ($page->slug) {
        'about-us' => ['eyebrow' => 'Our story & purpose', 'intro' => 'Celebrating authentic Nepalese heritage, culture, and craftsmanship with love.', 'icon' => 'bi-gem', 'stat' => 'Authentic & Handcrafted', 'cta' => ['Explore our collection', route('all-products')], 'secondary' => ['Contact us', route('contact')]],
        'academy-training' => ['eyebrow' => 'Craftsmanship & Skill', 'intro' => 'Dedicated skill development and heritage artistry inspired by authentic tradition.', 'icon' => 'bi-mortarboard', 'stat' => 'Hands-on learning', 'cta' => ['Talk to our team', route('contact')], 'secondary' => ['Shop products', route('all-products')]],
        'contact-us' => ['eyebrow' => 'We would love to hear from you', 'intro' => 'Questions about products, orders, or custom requests? Our friendly team is ready to help.', 'icon' => 'bi-chat-heart', 'stat' => 'Let’s connect', 'cta' => ['Send us a message', route('contact')], 'secondary' => ['Shop products', route('all-products')]],
        default => ['eyebrow' => 'Maitighar', 'intro' => $page->meta_description ?: 'Discover more from Maitighar.', 'icon' => 'bi-gem', 'stat' => 'Heritage with love', 'cta' => ['Shop all products', route('all-products')], 'secondary' => ['Contact us', route('contact')]],
    };
    $faqs = $page->faqs()->published()->get();
@endphp

<x-layouts.app :title="$page->meta_title ?: $page->title.' | Maitighar'" :meta-description="$page->meta_description ?: $pageDesign['intro']" :og-image="$page->getBannerUrl()" :canonical-url="route('pages.show', $page)">
    <div class="kba-cms-page kba-cms-page--{{ $page->slug }}">
        <section class="kba-cms-hero" aria-labelledby="page-title">
            <div class="kba-cms-hero-glow" aria-hidden="true"></div>
            <div class="kba-cms-hero-inner">
                <div class="kba-cms-hero-copy">
                    <nav class="kba-page-breadcrumbs" aria-label="Breadcrumb"><a wire:navigate href="{{ route('index') }}"><i class="bi bi-house-door" aria-hidden="true"></i>Home</a><i class="bi bi-chevron-right" aria-hidden="true"></i><span aria-current="page">{{ $page->title }}</span></nav>
                    <span class="kba-cms-eyebrow"><i class="bi {{ $pageDesign['icon'] }}" aria-hidden="true"></i>{{ $pageDesign['eyebrow'] }}</span>
                    <h1 id="page-title">{{ $page->title }}</h1>
                    <p>{{ $pageDesign['intro'] }}</p>
                    <div class="kba-cms-actions">
                        <a href="{{ $pageDesign['cta'][1] }}" class="kba-cms-button kba-cms-button--primary">{{ $pageDesign['cta'][0] }} <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                        <a href="{{ $pageDesign['secondary'][1] }}" class="kba-cms-button kba-cms-button--secondary">{{ $pageDesign['secondary'][0] }}</a>
                    </div>
                </div>
                <div class="kba-cms-hero-visual">
                    <img src="{{ $page->getBannerUrl() }}" alt="{{ $page->title }} at Maitighar" width="720" height="620">
                    <div class="kba-cms-hero-badge"><span><i class="bi {{ $pageDesign['icon'] }}" aria-hidden="true"></i></span><div><small>Maitighar</small><strong>{{ $pageDesign['stat'] }}</strong></div></div>
                </div>
            </div>
        </section>

        <section class="kba-cms-content" aria-label="{{ $page->title }} information">
            <div class="kba-cms-content-grid">
                <article class="kba-cms-article">
                    <header class="kba-cms-section-heading"><span>Discover more</span><h2>{{ $page->slug === 'about-us' ? 'Authentic craft and cultural heritage' : ($page->slug === 'contact-us' ? 'Visit or contact our team' : $page->title) }}</h2></header>
                    <div class="kba-cms-prose">{!! \App\Support\HtmlSanitizer::sanitize($page->content ?: '<p>More detail will be added soon.</p>') !!}</div>

                    @if($page->slug === 'about-us')
                        <div class="kba-cms-feature-grid">
                            <div><i class="bi bi-brush"></i><strong>Authentic artistry</strong><span>Handcrafted creations honoring traditional Nepali art and heritage.</span></div>
                            <div><i class="bi bi-people"></i><strong>Master artisans</strong><span>Crafted by skilled artisans with generations of heritage dedication.</span></div>
                            <div><i class="bi bi-heart"></i><strong>Made with love</strong><span>Every product tells a story of culture, community, and care.</span></div>
                        </div>
                    @elseif($page->slug === 'academy-training')
                        <div class="kba-cms-callout"><span><i class="bi bi-award"></i></span><div><small>Nepalese heritage with love</small><strong>Discover our authentic handcrafted collection</strong></div><a wire:navigate href="{{ route('all-products') }}">Explore shop <i class="bi bi-arrow-right"></i></a></div>
                    @elseif($page->slug === 'contact-us')
                        <div class="kba-contact-cards">
                            <a href="tel:{{ preg_replace('/[^+\d]/', '', \App\Models\Setting::get('store_phone', '+61 432 685 546')) }}"><i class="bi bi-telephone"></i><span><small>Call our team</small><strong>{{ \App\Models\Setting::get('store_phone', '+61 432 685 546') }}</strong></span></a>
                            <a href="mailto:{{ \App\Models\Setting::get('store_email', 'maitigharaustralia@gmail.com') }}"><i class="bi bi-envelope"></i><span><small>Email us</small><strong>{{ \App\Models\Setting::get('store_email', 'maitigharaustralia@gmail.com') }}</strong></span></a>
                            <div><i class="bi bi-geo-alt"></i><span><small>Find us</small><strong>{{ \App\Models\Setting::get('store_address', '2/75 Dartbrook Road, Auburn NSW, Sydney, NSW 2144') }}</strong></span></div>
                        </div>
                    @endif
                </article>

                <aside class="kba-cms-aside" aria-label="Helpful information">
                    @if($faqs->isNotEmpty())
                        <div class="kba-page-faq">
                            <div class="kba-page-faq-heading"><span class="kba-cms-eyebrow"><i class="bi bi-question-circle"></i> Helpful answers</span><h2>Common questions</h2><p>Quick answers to questions visitors often ask.</p></div>
                            <div class="kba-page-faq-list">
                                @foreach($faqs as $faq)
                                    <details class="kba-faq-item" @if($loop->first) open @endif><summary><span>{{ $faq->question }}</span><i class="bi bi-plus-lg" aria-hidden="true"></i></summary><div class="kba-faq-answer"><div>{!! \App\Support\HtmlSanitizer::sanitize(preg_match('/<[^>]+>/', $faq->answer) ? $faq->answer : nl2br(e($faq->answer))) !!}</div></div></details>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="kba-cms-support-card"><span class="kba-cms-support-icon"><i class="bi bi-headset"></i></span><small>Personal guidance</small><h2>Need a little help?</h2><p>Our team can guide you through our products, orders, and inquiries.</p><a wire:navigate href="{{ route('contact') }}">Talk to our team <i class="bi bi-arrow-right"></i></a></div>
                    @endif
                </aside>
            </div>
        </section>

        <section class="kba-cms-footer-cta">
            <div><span class="kba-cms-eyebrow"><i class="bi bi-gem"></i> Start your journey</span><h2>Experience Nepalese Heritage with Love.</h2><p>Explore our handcrafted collections, custom orders, or speak with our team.</p></div>
            <div class="kba-cms-actions"><a wire:navigate href="{{ route('contact') }}" class="kba-cms-button kba-cms-button--light">Contact us <i class="bi bi-arrow-up-right"></i></a><a wire:navigate href="{{ route('all-products') }}" class="kba-cms-button kba-cms-button--outline-light">Shop products</a></div>
        </section>
    </div>
</x-layouts.app>
