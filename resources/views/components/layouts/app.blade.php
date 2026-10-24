<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="theme-color" content="#EC0205">
        <meta name="robots" content="{{ $robots ?? (request()->routeIs('login', 'register', 'forgot-password', 'password.reset', 'verification.*', 'dashboard', 'profile.*', 'support-tickets', 'my-orders', 'my-order-details', 'checkout', 'success', 'cancelled') ? 'noindex, nofollow' : 'index, follow') }}">
        <meta name="description" content="{{ $metaDescription ?? 'Maitighar - Nepalese Heritage with Love. Authentic Nepalese heritage, culture, and craftsmanship.' }}">
        <meta property="og:type" content="{{ $ogType ?? 'website' }}">
        <meta property="og:title" content="{{ $title ?? 'Maitighar - Nepalese Heritage with Love' }}">
        <meta property="og:description" content="{{ $metaDescription ?? 'Maitighar - Nepalese Heritage with Love. Authentic Nepalese heritage, culture, and craftsmanship.' }}">
        <meta property="og:image" content="{{ $ogImage ?? asset('images/logo.png') }}">
        <meta property="og:url" content="{{ $canonicalUrl ?? url()->current() }}">
        <meta property="og:site_name" content="Maitighar">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $title ?? 'Maitighar - Nepalese Heritage with Love' }}">
        <meta name="twitter:description" content="{{ $metaDescription ?? 'Maitighar - Nepalese Heritage with Love. Authentic Nepalese heritage, culture, and craftsmanship.' }}">
        <meta name="twitter:image" content="{{ $ogImage ?? asset('images/logo.png') }}">
        <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

        <title>{{ $title ?? 'Maitighar - Nepalese Heritage with Love' }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
        <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700;9..40,800;9..40,900;9..40,1000&display=swap" onload="this.onload=null;this.rel='stylesheet'">
        <link rel="preload" as="style" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" onload="this.onload=null;this.rel='stylesheet'">
        <noscript>
            <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700;9..40,800;9..40,900;9..40,1000&display=swap">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        </noscript>

        @stack('head')

        @php
            $siteUrl = rtrim((string) config('app.url'), '/');
            $structuredData = [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => ['Store', 'EducationalOrganization'],
                        '@id' => $siteUrl.'/#organization',
                        'name' => 'Maitighar',
                        'url' => $siteUrl,
                        'logo' => asset('images/logo.png'),
                        'image' => asset('images/logo.png'),
                        'description' => 'Maitighar - Nepalese Heritage with Love. Authentic craftsmanship, services, and handcrafted collections.',
                        'telephone' => '+61432685546',
                        'email' => 'maitigharaustralia@gmail.com',
                        'priceRange' => 'AUD',
                        'address' => [
                            '@type' => 'PostalAddress',
                            'streetAddress' => '2/75 Dartbrook Road',
                            'addressLocality' => 'Auburn, Sydney',
                            'addressRegion' => 'NSW',
                            'postalCode' => '2144',
                            'addressCountry' => 'AU',
                        ],
                        'sameAs' => [
                            'https://www.facebook.com/maitigharaustralia',
                            'https://www.instagram.com/maitighar',
                        ],
                    ],
                    [
                        '@type' => 'WebSite',
                        '@id' => $siteUrl.'/#website',
                        'url' => $siteUrl,
                        'name' => 'Maitighar',
                        'publisher' => ['@id' => $siteUrl.'/#organization'],
                        'inLanguage' => 'en',
                    ],
                ],
            ];
            $analyticsId = (string) \App\Models\Setting::get('google_analytics_id', '');
        @endphp
        <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

        @if(preg_match('/^G-[A-Z0-9]+$/', $analyticsId))
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $analyticsId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '{{ $analyticsId }}', { anonymize_ip: true });
            </script>
        @endif

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="kba-site antialiased">
        @livewire('partials.navbar')

        <main>{{ $slot }}</main>

        @livewire('partials.footer')

        @livewire('partials.cart-modal')
        @livewire('partials.quick-view-modal')

        @livewireScripts
        @if(session()->has('livewire-alert'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.dispatchEvent(new CustomEvent('alert', { detail: @json(session('livewire-alert')) }));
                }, { once: true });
            </script>
        @endif

        @php $recaptchaService = app(App\Services\GoogleRecaptchaService::class); @endphp
        @if($recaptchaService->shouldProtect())
            <script src="{{ $recaptchaService->scriptSrc() }}" async defer></script>
            <script>
                window.kbaRecaptchaSiteKey = "{{ $recaptchaService->siteKey() }}";
                document.addEventListener('submit', function (event) {
                    const form = event.target;
                    if (! form.matches('[data-kba-recaptcha-form]')) {
                        return;
                    }

                    const tokenInput = form.querySelector('input[name="recaptchaToken"], input#recaptchaToken');
                    if (! tokenInput) {
                        return;
                    }

                    if (tokenInput.value.trim() !== '') {
                        return;
                    }

                    event.preventDefault();

                    if (typeof grecaptcha === 'undefined' || ! window.kbaRecaptchaSiteKey) {
                        return;
                    }

                    grecaptcha.ready(function () {
                        grecaptcha.execute(window.kbaRecaptchaSiteKey, { action: 'submit' }).then(function (token) {
                            if (! token) {
                                return;
                            }

                            tokenInput.value = token;
                            form.dataset.recaptchaProcessed = 'true';
                            form.submit();
                        });
                    });
                });
            </script>
        @endif
    </body>
</html>
