<?php

namespace App\Http\Middleware;

use App\Support\Storefront;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWishlistEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(Storefront::wishlistEnabled(), 404);

        return $next($request);
    }
}
