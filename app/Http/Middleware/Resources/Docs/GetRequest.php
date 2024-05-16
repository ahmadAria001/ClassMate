<?php

namespace App\Http\Middleware\Resources\Docs;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class GetRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if ($token === null) {
            abort(401, 'Unauthorized');
        }

        $pat = PersonalAccessToken::findToken($token);
        if (!$pat) abort(401, 'Unauthorized');;

        if ($pat->cant([\App\Http\Controllers\DocRequestController::class, 'get']) && !($pat->can('*'))) {
            return $next(null);
        }

        return $next($request);
    }
}