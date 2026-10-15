<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redirecting to eSewa</title>
    <style>
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 24px; background: #f8fafc; color: #0f172a; font-family: Arial, sans-serif; }
        main { width: min(440px, 100%); padding: 38px 30px; border: 1px solid #e2e8f0; border-radius: 24px; background: #fff; box-shadow: 0 20px 55px rgba(12, 30, 140, .08); text-align: center; }
        .mark { width: 64px; height: 64px; margin: 0 auto 20px; display: grid; place-items: center; border-radius: 50%; background: #60bb46; color: #fff; font-size: 28px; font-weight: 800; }
        h1 { margin: 0; font-size: 22px; }
        p { margin: 12px 0 0; color: #64748b; font-size: 13px; line-height: 1.6; }
        button { width: 100%; margin-top: 24px; padding: 13px 18px; border: 0; border-radius: 12px; background: #60bb46; color: #fff; cursor: pointer; font-size: 13px; font-weight: 700; }
        .loader { width: 30px; height: 30px; margin: 22px auto 0; border: 3px solid #e1eddc; border-top-color: #60bb46; border-radius: 50%; animation: spin .8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (prefers-reduced-motion: reduce) { .loader { animation: none; } }
    </style>
</head>
<body>
    <main>
        <div class="mark">e</div>
        <h1>Connecting to eSewa</h1>
        <p>Please wait while we securely transfer you to eSewa. Do not close or refresh this page.</p>
        <div class="loader" aria-hidden="true"></div>
        <form id="esewa-checkout-form" method="POST" action="{{ $action }}">
            @foreach($fields as $name => $value)
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @endforeach
            <button type="submit">Continue to eSewa</button>
        </form>
    </main>
    <script>document.getElementById('esewa-checkout-form').submit();</script>
</body>
</html>
