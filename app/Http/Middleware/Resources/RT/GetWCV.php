<?php

namespace App\Http\Middleware\Resources\RT;

use App\Http\Controllers\CivilianController;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class GetWCV
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

        if ($pat->cant('FinancialAssistanceController:get') && !($pat->can('*')) && $pat->cant('RtController:get') ) {
            abort(401, 'Unauthorized');
        }

        return $next($request);
    }
}
