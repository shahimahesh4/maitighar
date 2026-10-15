<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MinifyHtml
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (app()->runningUnitTests() || ! config('app.minify_html', true) || ! $this->isHtmlResponse($response)) {
            return $response;
        }

        $content = $response->getContent();

        if (! is_string($content) || $content === '') {
            return $response;
        }

        // Preserve one separator between tags so adjacent inline elements keep
        // their intended spacing while indentation and blank lines disappear.
        $content = preg_replace('/>\s+</u', '> <', $content) ?? $content;
        $content = preg_replace('/<!--(?!\[if\b).*?-->/su', '', $content) ?? $content;

        $response->setContent(trim($content));

        return $response;
    }

    private function isHtmlResponse(Response $response): bool
    {
        return str_contains((string) $response->headers->get('Content-Type'), 'text/html');
    }
}
