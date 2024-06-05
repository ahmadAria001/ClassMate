<?php

namespace App\Utils;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AccessToken
{
    public static function getToken(Request $request): PersonalAccessToken|null
    {
        $token = null;
        if (str_contains($request->url(), 'api')) {
            $token = $request->bearerToken();
            if (!$token) {
                $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                if (!$token) {
                    return redirect('login');
                }
            }
        } else {
            $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

            if (!$token) {
                return redirect('login');
            }
        }

        return PersonalAccessToken::findToken($token);
    }
}
