<?php

namespace App\Http\Middleware\Resources\Docs;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class DeleteDocumentation
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
        if (!$pat) abort(401, 'Unauthorized');

        if ($pat->cant('DocumentationController:destroy') && !($pat->can('*'))) {
            abort(401, 'Unauthorized');
        }

        return $next($request);
    }
}
