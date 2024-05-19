<?php

namespace App\Http\Middleware\Resources\Docs;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class GetComplaint
{
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

        if ($pat->cant('ComplaintController:get') && !$pat->can('*')) {
            abort(401, 'Unauthorized');
        }

        return $next($request);
    }
}
