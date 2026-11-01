@php
    $setting = static fn (string $key, mixed $default = null): mixed => \App\Models\Setting::get($key, $default);
    $storeName = (string) $setting('store_name', 'Maitighar');
    $storePhone = (string) $setting('store_phone', '+61 432 685 546');
    $storeEmail = (string) $setting('store_email', 'maitigharaustralia@gmail.com');
    $storeAddress = (string) $setting('store_address', '2/75 Dartbrook Road, Auburn NSW, Sydney, NSW 2144');
    $customLogo = (string) $setting('coming_soon_logo', '');
    $storeLogo = (string) $setting('store_logo', '');
    $favicon = (string) $setting('store_favicon', '');
    $logoUrl = $customLogo !== '' ? \Illuminate\Support\Facades\Storage::disk('public')->url($customLogo) : ($storeLogo !== '' ? \Illuminate\Support\Facades\Storage::disk('public')->url($storeLogo) : asset('images/logo.webp'));
    $faviconUrl = $favicon !== '' ? \Illuminate\Support\Facades\Storage::disk('public')->url($favicon) : asset('favicon-32x32.png');
    $values = collect($setting('coming_soon_values', ['Nepalese Heritage', 'Culture', 'Craftsmanship']))
        ->map(fn ($value) => is_array($value) ? ($value['value'] ?? '') : $value)
        ->filter(fn ($value) => filled($value));
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fffaf2">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="{{ $setting('coming_soon_description', 'Nepalese heritage, culture, and craftsmanship — soon at home in Australia.') }}">
    <title>{{ $setting('coming_soon_heading', 'Coming Soon') }} — {{ $storeName }}</title>
    <link rel="icon" href="{{ $faviconUrl }}" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root { --ink: #0a2072; --red: #ed080c; --cream: #fffaf2; --muted: #79696a; }
        * { box-sizing: border-box; }
        body { min-width: 320px; margin: 0; color: var(--ink); background: var(--cream); font-family: "DM Sans", sans-serif; }
        .launch { position: relative; isolation: isolate; display: grid; min-height: calc(100svh - 51px); overflow: hidden; place-items: center; padding: 27px 20px 42px; }
        .orb { position: absolute; z-index: -1; border: 1px solid rgba(237, 8, 12, .14); border-radius: 50%; pointer-events: none; }
        .orb--top { width: 710px; height: 710px; top: -390px; left: -350px; }
        .orb--bottom { width: 680px; height: 680px; right: -410px; bottom: -350px; border-color: rgba(10, 32, 114, .13); }
        .orb--left { width: 190px; height: 190px; left: -105px; bottom: -108px; border-color: rgba(237, 8, 12, .18); }
        .leaf { position: absolute; z-index: -1; width: 18px; height: 43px; border-radius: 100% 0 100% 0; background: rgba(237, 8, 12, .18); transform: rotate(42deg); }
        .leaf--a { left: 11%; top: 27%; }
        .leaf--b { right: 11%; bottom: 31%; width: 16px; height: 42px; background: rgba(10, 32, 114, .17); transform: rotate(29deg); }
        .leaf--c { right: 19%; top: 15%; width: 13px; height: 28px; background: rgba(235, 187, 49, .21); transform: rotate(86deg); }
        .corner { position: absolute; z-index: -1; width: 105px; height: 105px; opacity: .14; background: var(--red); transform: rotate(45deg); }
        .corner--left { left: -73px; top: 163px; }
        .corner--right { right: -72px; bottom: 154px; background: var(--ink); }
        .content { width: min(100%, 855px); text-align: center; }
        .eyebrow { display: inline-flex; align-items: center; gap: 12px; color: var(--ink); font-size: 11px; font-weight: 700; letter-spacing: .22em; text-transform: uppercase; }
        .eyebrow::before { color: var(--red); content: "✦"; font-size: 15px; }
        .eyebrow .line { width: 34px; height: 1px; background: #bbc2d5; }
        .logo-wrap { display: grid; width: 190px; height: 190px; margin: 28px auto 31px; place-items: center; overflow: hidden; border: 2px solid rgba(237, 8, 12, .16); border-radius: 50%; background: var(--red); box-shadow: 0 19px 35px rgba(38, 31, 31, .10); }
        .logo-wrap img { width: 150px; height: 150px; background: var(--red); object-fit: contain; }
        .kicker { margin: 0 0 11px; color: var(--red); font-size: 12px; font-weight: 700; letter-spacing: .27em; text-transform: uppercase; }
        h1, h2 { font-family: "Playfair Display", Georgia, serif; }
        h1 { margin: 0; color: var(--ink); font-size: clamp(4rem, 7.2vw, 6.4rem); font-weight: 700; letter-spacing: -.075em; line-height: .88; }
        .flourish { display: flex; align-items: center; justify-content: center; gap: 11px; margin: 18px auto 21px; color: var(--red); }
        .flourish::before, .flourish::after { width: 48px; height: 1px; background: linear-gradient(90deg, transparent, #ef7b7d); content: ""; }
        .flourish::after { transform: scaleX(-1); }
        h2 { margin: 0; font-size: clamp(1.65rem, 3vw, 2.25rem); letter-spacing: -.035em; }
        .intro { max-width: 670px; margin: 7px auto 22px; color: var(--muted); font-size: clamp(.95rem, 1.7vw, 1.08rem); line-height: 1.5; }
        .facebook { display: inline-flex; align-items: center; gap: 10px; padding: 13px 20px 13px 13px; border-radius: 999px; color: white; background: #1977ee; box-shadow: 0 9px 17px rgba(25, 119, 238, .23); font-size: .94rem; font-weight: 700; text-decoration: none; transition: transform .18s ease, box-shadow .18s ease; }
        .facebook:hover { transform: translateY(-2px); box-shadow: 0 12px 20px rgba(25, 119, 238, .32); }
        .facebook:focus-visible { outline: 3px solid var(--ink); outline-offset: 4px; }
        .facebook__icon { display: grid; width: 25px; height: 25px; place-items: center; border-radius: 50%; background: #fff; color: #1977ee; font-family: Arial, sans-serif; font-size: 20px; font-weight: 700; }
        .values { display: flex; justify-content: center; flex-wrap: wrap; gap: 0; margin: 37px 0 22px; color: #816e70; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .values span { padding: 0 15px; border-right: 1px solid #d9d0cc; }
        .values span:last-child { border-right: 0; }
        .contact-card { display: grid; grid-template-columns: repeat(3, 1fr); overflow: hidden; border: 1px solid #e0e3e9; border-radius: 20px; background: rgba(255, 255, 255, .76); box-shadow: 0 16px 32px rgba(67, 47, 39, .08); font-style: normal; text-align: left; }
        .contact { display: flex; gap: 13px; min-height: 88px; padding: 20px 22px; align-items: flex-start; }
        .contact + .contact { border-left: 1px solid #e0e3e9; }
        .contact svg { flex: 0 0 auto; width: 20px; height: 20px; color: var(--red); }
        .contact b { display: block; margin-bottom: 3px; font-size: 12px; letter-spacing: .12em; text-transform: uppercase; }
        .contact p { margin: 0; color: #695b5b; font-size: 13px; line-height: 1.4; }
        .launch-footer { display: flex; min-height: 51px; align-items: center; justify-content: center; gap: 15px; padding: 12px 20px; color: #7f7070; background: rgba(255,255,255,.9); font-size: 12px; }
        .launch-footer strong { color: var(--ink); }.launch-footer .star { color: var(--red); }
        @media (min-width: 681px) {
            html, body { height: 100%; overflow: hidden; }
            .launch { height: calc(100svh - 43px); min-height: 0; padding: 14px 20px 18px; }
            .logo-wrap { width: 155px; height: 155px; margin: 12px auto 14px; }
            .logo-wrap img { width: 122px; height: 122px; }
            .kicker { margin-bottom: 6px; font-size: 10px; }
            h1 { font-size: clamp(3.7rem, 5.7vw, 5.5rem); }
            .flourish { margin: 9px auto 12px; }
            h2 { font-size: clamp(1.5rem, 2.2vw, 2rem); }
            .intro { margin: 4px auto 13px; font-size: .93rem; }
            .facebook { padding: 10px 17px 10px 11px; font-size: .84rem; }
            .facebook__icon { width: 22px; height: 22px; font-size: 17px; }
            .values { margin: 18px 0 13px; font-size: 10px; }
            .contact { min-height: 70px; padding: 15px 19px; }
            .contact p { font-size: 12px; }
            .launch-footer { min-height: 43px; padding: 8px 20px; font-size: 11px; }
        }
        @media (max-width: 680px) { .launch { min-height: auto; padding: 22px 16px 30px; } .eyebrow { font-size: 9px; gap: 8px; letter-spacing: .16em; } .logo-wrap { width: 120px; height: 120px; margin-top: 20px; margin-bottom: 21px; } .logo-wrap img { width: 96px; height: 96px; } .kicker { font-size: 10px; letter-spacing: .19em; } .contact-card { grid-template-columns: 1fr; border-radius: 16px; } .contact { min-height: auto; padding: 15px 18px; } .contact + .contact { border-top: 1px solid #e0e3e9; border-left: 0; } .values { margin: 25px 0 18px; row-gap: 8px; } .values span { padding: 0 9px; font-size: 9px; } .launch-footer { flex-direction: column; gap: 4px; text-align: center; font-size: 11px; } }
    </style>
</head>
<body>
    <main class="launch">
        <i class="orb orb--top"></i><i class="orb orb--bottom"></i><i class="orb orb--left"></i>
        <i class="leaf leaf--a"></i><i class="leaf leaf--b"></i><i class="leaf leaf--c"></i><i class="corner corner--left"></i><i class="corner corner--right"></i>
        <section class="content" aria-labelledby="coming-soon-heading">
            <p class="eyebrow">{{ $setting('coming_soon_topline_left', 'From Nepal, with Love') }} <span class="line"></span> {{ $setting('coming_soon_topline_right', 'Australia') }}</p>
            <div class="logo-wrap"><img src="{{ $logoUrl }}" alt="{{ $storeName }} logo"></div>
            <p class="kicker">{{ $setting('coming_soon_eyebrow', 'Nepalese Heritage with Love') }}</p>
            <h1 id="coming-soon-heading">{{ $setting('coming_soon_heading', 'Coming Soon') }}</h1>
            <div class="flourish" aria-hidden="true">✦</div>
            <h2>{{ $setting('coming_soon_subheading', 'A beautiful new chapter is coming.') }}</h2>
            <p class="intro">{{ $setting('coming_soon_description', 'Nepalese heritage, culture, and craftsmanship — soon at home in Australia.') }}</p>
            @if($facebookUrl = $setting('facebook_url', 'https://www.facebook.com/maitigharaustralia'))
                <a class="facebook" href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer"><span class="facebook__icon">f</span> {{ $setting('coming_soon_facebook_label', 'Follow us on Facebook') }} <span aria-hidden="true">↗</span></a>
            @endif
            <div class="values" aria-label="Our values">@foreach($values as $value)<span>{{ $value }}</span>@endforeach</div>
            <address class="contact-card">
                <div class="contact"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.4 1.8.6 2.8.7a2 2 0 0 1 1.8 2.1Z"/></svg><div><b>Call us</b><p>{{ $storePhone }}</p></div></div>
                <div class="contact"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg><div><b>Email us</b><p>{{ $storeEmail }}</p></div></div>
                <div class="contact"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2"/></svg><div><b>Find us</b><p>{!! nl2br(e($storeAddress)) !!}</p></div></div>
            </address>
        </section>
    </main>
    <footer class="launch-footer"><span>{{ $setting('coming_soon_footer_text', '© 2026 Maiti Ghar · Made with heritage and heart') }}</span><span><i class="star">✦</i>&nbsp; <strong>{{ $setting('coming_soon_footer_credit', 'Crafted by Siddhi Tech Nepal') }}</strong></span></footer>
</body>
</html>
