<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(self)');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'self'; form-action 'self' https://epay.esewa.com.np https://rc-epay.esewa.com.np; img-src 'self' data: blob: https:; font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://www.gstatic.com https://cdn.jsdelivr.net https://www.googletagmanager.com; connect-src 'self' https://www.google.com https://www.gstatic.com https://www.google-analytics.com https://region1.google-analytics.com; frame-src https://www.google.com https://recaptcha.google.com;"
        );

        $forwardedHttps = strtolower((string) $request->header('X-Forwarded-Proto')) === 'https';

        if (app()->isProduction() && ($request->isSecure() || $forwardedHttps)) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
