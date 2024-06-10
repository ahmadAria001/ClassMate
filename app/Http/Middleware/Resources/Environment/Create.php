<?php

namespace App\Http\Middleware\Resources\Environment;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class Create
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

            if (!$token) {
                abort(401, 'Unauthorized');
            }
        }

        $pat = PersonalAccessToken::findToken($token);
        if (!$pat) {
            abort(401, 'Unauthorized');
        }

        if ($pat->cant('NewsController:create') && !$pat->can('*')) {
            return $next(null);
        }

        return $next($request);
    }
}
