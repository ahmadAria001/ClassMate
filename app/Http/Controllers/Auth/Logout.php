<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class Logout extends Controller
{
    public function __invoke(Request $req)
    {
        if (str_contains($req->url(), 'api')) {
            $token = $req->bearerToken();

            if (!$token) {
                $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

                if (!$token) {
                    return Response()->json(
                        [
                            'message' => 'Unauthorized',
                        ],
                        401,
                    );
                }
            }

            setcookie('token', '', time() - 3600, '/');

            $pat = PersonalAccessToken::findToken($token);
            $pat->delete();

            error_log($req->getBaseUrl());

            if (str_contains($req->header('referer'), $req->getBaseUrl())) {
                return redirect('login');
            }
        } else {
            if (!Auth::hasUser()) {
                return Response()->json(
                    [
                        'message' => 'Unauthorized',
                    ],
                    401,
                );
            }

            Auth::logout();
        }
    }
}
