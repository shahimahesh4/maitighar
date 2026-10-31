<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>{{ $subjectLine }}</title></head>
<body style="margin:0;padding:0;background:#f8fafc;color:#0f172a;font-family:Arial,Helvetica,sans-serif;-webkit-font-smoothing:antialiased">
@php($store = app(\App\Services\CommunicationService::class)->store())
<div style="display:none;max-height:0;overflow:hidden;opacity:0">{{ $intro }}&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;</div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f8fafc">
<tr><td align="center" style="padding:32px 12px">
    <table role="presentation" width="640" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:640px">
        <tr><td align="center" style="padding:0 0 20px"><img src="{{ asset('images/logo.webp') }}" width="82" alt="{{ $store['name'] }}" style="display:block;width:82px;height:auto"></td></tr>
        <tr><td style="overflow:hidden;border:1px solid #e2e8f0;border-radius:24px;background:#ffffff;box-shadow:0 18px 48px rgba(12,30,140,.08)">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr><td style="padding:42px 42px 38px;background:#0C1E8C;text-align:center;color:#fff">
                    <div style="margin-bottom:13px;color:#ffffff;opacity:0.9;font-size:11px;font-weight:bold;letter-spacing:2.4px;text-transform:uppercase">{{ $store['name'] }}</div>
                    <h1 style="margin:0;font-family:Georgia,'Times New Roman',serif;font-size:32px;font-weight:normal;line-height:1.2">{{ $heading }}</h1>
                </td></tr>
                <tr><td style="padding:38px 42px 14px">
                    <p style="margin:0;color:#334155;font-size:16px;line-height:1.75">{{ $intro }}</p>
                </td></tr>
                @if($details)
                <tr><td style="padding:18px 42px">
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="overflow:hidden;border:1px solid #e2e8f0;border-radius:16px">
                        @foreach($details as $label => $value)
                        <tr>
                            <td valign="top" style="width:36%;padding:13px 16px;border-bottom:1px solid #f1f5f9;background:#f8fafc;color:#0C1E8C;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:.4px">{{ $label }}</td>
                            <td valign="top" style="padding:13px 16px;border-bottom:1px solid #f1f5f9;color:#0f172a;font-size:14px;line-height:1.5">{{ $value }}</td>
                        </tr>
                        @endforeach
                    </table>
                </td></tr>
                @endif
                @if($messageText)
                <tr><td style="padding:8px 42px 16px"><div style="padding:18px 20px;border-left:4px solid #EC0205;border-radius:0 12px 12px 0;background:#f8fafc;color:#334155;font-size:14px;line-height:1.7">{!! nl2br(e($messageText)) !!}</div></td></tr>
                @endif
                @if($actionUrl && $actionLabel)
                <tr><td align="center" style="padding:18px 42px 34px"><a href="{{ $actionUrl }}" style="display:inline-block;padding:14px 24px;border-radius:999px;background:#EC0205;color:#fff;font-size:14px;font-weight:bold;text-decoration:none">{{ $actionLabel }} &nbsp;→</a></td></tr>
                @endif
                <tr><td style="padding:24px 42px;border-top:1px solid #e2e8f0;background:#f8fafc;text-align:center;color:#64748b;font-size:12px;line-height:1.7">
                    Questions? Reply to this email or call <strong style="color:#0f172a">{{ $store['phone'] }}</strong>.<br>
                    {{ $store['footer'] }}
                </td></tr>
            </table>
        </td></tr>
        <tr><td align="center" style="padding:22px 24px 0;color:#64748b;font-size:11px;line-height:1.6">{{ $store['address'] }}<br><a href="mailto:{{ $store['email'] }}" style="color:#0C1E8C;text-decoration:none">{{ $store['email'] }}</a><br>&copy; {{ now()->year }} {{ $store['name'] }}</td></tr>
    </table>
</td></tr>
</table>
</body>
</html>
