<?php

namespace App\Http\Middleware;

use App\Models\Civilian;
use App\Models\User;
use Illuminate\Http\Request;
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
                // $request->user() ? $request->user()->only('id', 'username', 'role') : null
                $token = $request->cookie('token');

                if ($token) {
                    $pat = PersonalAccessToken::findToken($token);

                    $model = $pat->tokenable();
                    $userID = $model->get()->first()->civilian_id;
                    $user = Civilian::where('id', '=', $userID)->first();

                    return [
                        'username' => $model->get('username')->first()->username,
                        'role' => $model->get('role')->first()->role,
                        'id' => $userID,
                        'fullName' => $user->fullName,
                        'nik' => $user->nik,
                    ];
                } else {
                    return $request->user() ? $request->user()->only('id', 'username', 'role') : null;
                }
            },
        ]);
    }
}
