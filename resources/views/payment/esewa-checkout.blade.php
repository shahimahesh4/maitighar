<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Redirecting to eSewa</title></head>
<body><p>Redirecting securely to eSewa…</p><form id="esewa" method="POST" action="{{ $checkout['action'] }}">@foreach($checkout['fields'] as $name => $value)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endforeach<noscript><button type="submit">Continue to eSewa</button></noscript></form><script>document.getElementById('esewa').submit();</script></body></html>
