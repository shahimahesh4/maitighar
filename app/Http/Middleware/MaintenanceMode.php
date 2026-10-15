<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldBypass($request) || ! $this->isEnabled()) {
            return $next($request);
        }

        return response()
            ->view('maintenance', [
                'title' => Setting::get('maintenance_title', 'Maitighar update is underway'),
                'message' => Setting::get(
                    'maintenance_message',
                    'We are polishing the website to give you a better experience. Please check back soon.',
                ),
                'storeName' => Setting::get('store_name', 'Maitighar'),
                'storeEmail' => Setting::get('store_email', 'maitigharaustralia@gmail.com'),
            ], Response::HTTP_SERVICE_UNAVAILABLE)
            ->header('Retry-After', '3600');
    }

    private function shouldBypass(Request $request): bool
    {
        return $request->is(
            'stnapanel',
            'stnapanel/*',
            'livewire/*',
            'payment/webhook/*',
            'payment/callback/*',
            'up',
        );
    }

    private function isEnabled(): bool
    {
        try {
            return filter_var(Setting::get('maintenance_mode', false), FILTER_VALIDATE_BOOL);
        } catch (\Throwable) {
            return false;
        }
    }
}
