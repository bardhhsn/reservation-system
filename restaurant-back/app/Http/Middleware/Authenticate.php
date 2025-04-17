<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request): ?string
    {
        // Në rast se kërkesa është JSON (API), mos e kthe te route 'login' sepse s’ka
        if (! $request->expectsJson()) {
            return null;
        }

        return null;
    }
}
