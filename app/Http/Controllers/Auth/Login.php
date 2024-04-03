<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Login as AuthLogin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class Login extends Controller
{
    private $errMsg = 'Username atau Password tidak ditemukan';

    public function __invoke(AuthLogin $req)
    {
        try {
            $req->authenticate();

            if (str_contains($req->url(), 'api')) {
                $user = User::where('username', $req->only('username'))->first();

                $token_abilities = null;
                if ($user->role === 'Admin') $token_abilities = ['*'];
                if ($user->role === 'RT') $token_abilities = ['*'];
                if ($user->role === 'RW') $token_abilities = ['*'];
                if ($user->role === 'Warga') $token_abilities = ['complaint:view,create,edit,destroy'];

                $generatedToken = $user->createToken('access_token_', $token_abilities, now()->addWeek())->plainTextToken;
                return Response()->json(['status' => true, 'token' => $generatedToken]);
            }

            $user = Auth::attempt($req->only('username', 'password'));
            return Inertia::render('/Dashboard');
        } catch (\Throwable $th) {
            if (str_contains($req->url(), 'api')) {
                return Response()->json(['status' => false, 'err' => $this->errMsg], 404);
            }

            return back()->with(['status' => false, 'err' => $this->errMsg]);
        }
    }
}
