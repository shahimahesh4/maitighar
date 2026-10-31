<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#EC0205">
    <title>{{ $title }} | {{ $storeName }}</title>
    <style>
        :root { color-scheme: light; --cream:#f8fafc; --paper:#ffffff; --gold:#EC0205; --gold-dark:#0C1E8C; --ink:#0f172a; --muted:#64748b; }
        * { box-sizing: border-box; }
        body { margin:0; min-height:100vh; display:grid; place-items:center; padding:24px; color:var(--ink); background:radial-gradient(circle at 15% 15%,rgba(12,30,140,0.06) 0,transparent 36%),radial-gradient(circle at 85% 80%,rgba(236,2,5,0.06) 0,transparent 34%),var(--cream); font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif; }
        main { width:min(720px,100%); position:relative; overflow:hidden; padding:clamp(32px,7vw,68px); text-align:center; background:#ffffff; border:1px solid #e2e8f0; border-radius:30px; box-shadow:0 28px 80px rgba(12,30,140,.1); }
        main::before { content:""; position:absolute; inset:0 0 auto; height:5px; background:linear-gradient(90deg,var(--gold),var(--gold-dark)); }
        .logo { width:92px; height:92px; object-fit:contain; margin-bottom:20px; }
        .eyebrow { margin:0 0 14px; color:var(--gold-dark); font-size:12px; font-weight:800; letter-spacing:.2em; text-transform:uppercase; }
        h1 { max-width:560px; margin:0 auto; font-family:Georgia,"Times New Roman",serif; font-size:clamp(34px,7vw,58px); line-height:1.05; letter-spacing:-.035em; }
        .message { max-width:540px; margin:24px auto 0; color:var(--muted); font-size:clamp(16px,2.4vw,19px); line-height:1.75; }
        .status { display:inline-flex; align-items:center; gap:10px; margin-top:30px; padding:10px 16px; color:var(--gold-dark); background:#eff6ff; border-radius:999px; font-size:13px; font-weight:750; }
        .dot { width:9px; height:9px; border-radius:50%; background:var(--gold); box-shadow:0 0 0 5px rgba(236,2,5,.18); }
        footer { margin-top:34px; padding-top:24px; border-top:1px solid #e2e8f0; color:var(--muted); font-size:14px; }
        a { color:var(--gold-dark); font-weight:750; text-decoration-thickness:1px; text-underline-offset:3px; }
        @media (prefers-reduced-motion:no-preference) { .dot { animation:pulse 2s ease-in-out infinite; } @keyframes pulse { 50% { box-shadow:0 0 0 9px rgba(236,2,5,.08); } } }
    </style>
</head>
<body>
    <main role="main" aria-labelledby="maintenance-title">
        <img class="logo" src="{{ asset('images/logo.webp') }}" alt="{{ $storeName }}">
        <p class="eyebrow">Website maintenance</p>
        <h1 id="maintenance-title">{{ $title }}</h1>
        <p class="message">{{ $message }}</p>
        <div class="status"><span class="dot" aria-hidden="true"></span>We’ll be back soon</div>
        <footer>
            Need help now? <a href="mailto:{{ $storeEmail }}">{{ $storeEmail }}</a>
        </footer>
    </main>
</body>
</html>
