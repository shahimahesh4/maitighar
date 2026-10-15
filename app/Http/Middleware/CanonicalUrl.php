<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanonicalUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->isProduction()) {
            return $next($request);
        }

        $configuredUrl = parse_url((string) config('app.url'));
        $scheme = $configuredUrl['scheme'] ?? 'https';
        $host = $configuredUrl['host'] ?? $request->getHost();
        $port = isset($configuredUrl['port']) ? ':'.$configuredUrl['port'] : '';
        $forwardedScheme = strtolower((string) $request->header('X-Forwarded-Proto'));
        $requestIsSecure = $request->isSecure() || $forwardedScheme === 'https';

        if ($request->getHost() !== $host || ($scheme === 'https' && ! $requestIsSecure)) {
            $status = in_array($request->method(), ['GET', 'HEAD'], true) ? 301 : 308;

            return redirect()->away($scheme.'://'.$host.$port.$request->getRequestUri(), $status);
        }

        return $next($request);
    }
}
