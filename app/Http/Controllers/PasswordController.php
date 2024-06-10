<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Password\Reset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class PasswordController extends Controller
{
    public function reset(Reset $req)
    {
        $payload = $req->getPayload();

        $identity = null;
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

            $pat = PersonalAccessToken::findToken($token);
            $identity = $pat->tokenable()->get('id')[0]->id;
        } else {
            $identity = Auth::id();
        }

        $model = User::withoutTrashed()->where('id', $identity)->get()->first();

        if (!Hash::check($payload->get('password'), $model->getAuthPassword())) {
            return Response()->json(
                [
                    'status' => false,
                    'message' => 'Password saat ini tidak cocok',
                ],
                400,
            );
        }

        $model->fill(['password' => Hash::make($payload->get('new_password'))]);
        $model->save();

        return Response()->json([
            'status' => true,
            'message' => 'Password berhasil dirubah',
        ]);
    }
}
