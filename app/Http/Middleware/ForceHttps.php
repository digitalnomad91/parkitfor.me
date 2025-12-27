<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldRedirect($request)) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }

    protected function shouldRedirect(Request $request): bool
    {
        if (app()->runningInConsole()) {
            return false;
        }

        if (app()->environment('local', 'testing')) {
            return false;
        }

        return ! $request->secure();
    }
}
