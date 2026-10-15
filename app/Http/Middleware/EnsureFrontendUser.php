<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFrontendUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->user()?->role, ['super_admin', 'admin', 'trainer', 'staff'], true)) {
            return redirect('/stnapanel');
        }

        return $next($request);
    }
}
