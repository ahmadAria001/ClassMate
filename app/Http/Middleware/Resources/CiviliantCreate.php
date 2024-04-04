<?php

namespace App\Http\Middleware\Resources;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class CiviliantCreate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $token = $request->bearerToken();

        // error_log($token);
        if ($token === null) {
            abort(401, 'Unauthorized');
        }

        $pat = PersonalAccessToken::findToken($token);

        // error_log($pat->can([\App\Http\Controllers\CivilianController::class, 'create']));
        // error_log($pat->can('*'));

        if ($pat->cant([\App\Http\Controllers\CivilianController::class, 'create']) && !($pat->can('*'))) {
            return $next(null);
        }

        return $next($request);
    }
}
