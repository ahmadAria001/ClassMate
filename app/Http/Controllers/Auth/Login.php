<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Login as AuthLogin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Inertia\Inertia;

class Login extends Controller
{
    private $errMsg = 'Username atau Password tidak ditemukan';

    public function __invoke(AuthLogin $req)
    {
        try {
            $req->authenticate();

            if (str_contains($req->url(), 'api')) {
                $credentals = $req->only('username', 'password');
                $user = null;

                // error_log($credentals['username']);
                if (is_numeric($credentals['username'])) {
                    $user = User::with('civilian_id')
                        ->whereHas('civilian_id', function ($query) use ($credentals) {
                            $query->where('nik', '=', $credentals['username']);
                        })
                        ->first();
                } else {
                    $user = User::where('username', $credentals['username'])->first();
                }

                $token_abilities = null;
                if ($user->role === 'Admin') {
                    $token_abilities = ['*'];
                }
                if ($user->role === 'RT') {
                    $token_abilities = ['*'];
                }
                if ($user->role === 'RW') {
                    $token_abilities = ['*'];
                }
                if ($user->role === 'Warga') {
                    $token_abilities = ['complaint:view,create,edit,destroy'];
                }

                $generatedToken = $user->createToken('access_token', $token_abilities, now()->addWeek());
                $cookie = Cookie('token', $generatedToken->plainTextToken, Carbon::now()->addWeek()->getTimestamp(), '/', null, null, true);

                error_log($generatedToken->plainTextToken);

                return Response()
                    ->json([
                        'status' => true,
                        'token' => $generatedToken->plainTextToken,
                        'exp' => now()->addWeek()->timestamp,
                    ])
                    ->cookie($cookie);
            }

            $user = Auth::attempt($req->only('username', 'password'));
            return Inertia::render('Auth/Civilian');
        } catch (\Throwable $th) {
            // error_log($th);

            if (str_contains($req->url(), 'api')) {
                return Response()->json(['status' => false, 'err' => $this->errMsg], 404);
            }

            return back()->with(['status' => false, 'err' => $this->errMsg]);
        }
    }
}
