<?php

namespace App\Http\Middleware\Resources;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class UpdateDues
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

        if ($pat->cant([\App\Http\Controllers\DuesController::class, 'edit']) && !($pat->can('*'))) {
            return $next(null);
        }

        return $next($request);
    }
}
