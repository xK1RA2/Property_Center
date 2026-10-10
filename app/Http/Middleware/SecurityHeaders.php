<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Browser hardening. The Blade pages rely on inline scripts and third-party CDNs, so a strict
 * script-src would break them; the directives below are the ones that do NOT need that and still
 * remove whole classes of attack (framing, <base> hijacking, forms posting to other sites, plugins).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = $response->headers;

        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self), payment=()');
        $headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');

        if (!$headers->has('Content-Security-Policy')) {
            $headers->set(
                'Content-Security-Policy',
                "base-uri 'self'; form-action 'self'; frame-ancestors 'self'; object-src 'none'"
            );
        }

        // Laravel's session middleware already sends "no-cache, private", which still lets the browser
        // STORE the page. Anything shown to a signed-in user must not be kept at all.
        if ($request->user()) {
            $headers->set('Cache-Control', 'no-store, private');
        }

        // HSTS is only valid (and only safe) over HTTPS
        if ($request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
