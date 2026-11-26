<?php

namespace App\Http\Middleware;

use App\Support\Storefront;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCommerceEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Storefront::commerceEnabled()) {
            return redirect()->route('contact')->with('contact_notice', 'Online ordering is currently unavailable. Please request a quote and our team will assist you.');
        }

        return $next($request);
    }
}
