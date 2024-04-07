<?php

namespace App\Http\Middleware\Resources\Family;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class Delete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if ($token === null) {
            abort(401, 'Unauthorized');
        }

        $pat = PersonalAccessToken::findToken($token);
        if (!$pat) abort(401, 'Unauthorized');;

        if ($pat->cant([\App\Http\Controllers\FamilyController::class, 'destroy']) && !($pat->can('*'))) {
            return $next(null);
        }

        return $next($request);
    }
}