<?php

namespace App\Http\Middleware;

use App\Models\Civilian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Inertia\Inertia;
use Inertia\Middleware;
use Laravel\Sanctum\PersonalAccessToken;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'appName' => config('app.name'),

            // Lazily...
            'auth.user' => function () use ($request) {
                // $request->user() ? $request->user()->only('id', 'username', 'role') : nul
                $token = $request->bearerToken();

                if (!$token) {
                    $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

                    if (!$token) {
                        return [
                            'username' => null,
                            'role' => null,
                            'id' => null,
                            'fullName' => null,
                            'nik' => null,
                            'rt_id' => null,
                        ];
                    }
                }

                if ($token) {
                    $pat = PersonalAccessToken::findToken($token);

                    if (!$pat) {
                        return [
                            'username' => null,
                            'role' => null,
                            'id' => null,
                            'fullName' => null,
                            'nik' => null,
                            'rt_id' => null,
                        ];
                    }

                    $model = $pat->tokenable();

                    $userID = $model->get()->first()->civilian_id;
                    $user = Civilian::with('rt_id')->where('id', '=', $userID)->first();
                    $rt = $user->rt_id;

                    return [
                        'username' => $model->get('username')->first()->username,
                        'role' => $model->get('role')->first()->role,
                        'id' => $userID,
                        'fullName' => $user->fullName,
                        'nik' => $user->nik,
                        'rt_id' => $rt,
                    ];
                } else {
                    return $request->user() ? $request->user()->only('id', 'username', 'role') : null;
                }
            },
        ]);
    }
}
