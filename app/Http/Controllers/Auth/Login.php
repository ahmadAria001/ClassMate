<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Http\Requests\Auth\Login as AuthLogin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Inertia\Inertia;
use ReflectionClass;

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
                    $token_abilities = [
                        '*'
                    ];
                }
                if ($user->role === 'RT') {
                    $token_abilities = [
                        'ComplaintController:__invoke', 'ComplaintController:get', 'ComplaintController:create', 'ComplaintController:edit', 'ComplaintController:destroy',
                        'ActivityController:__invoke', 'ActivityController:get', 'ActivityController:create', 'ActivityController:edit', 'ActivityController:destroy',
                        'DocumentationController:__invoke', 'DocumentationController:get', 'DocumentationController:create', 'DocumentationController:edit', 'DocumentationController:destroy',
                        'DocRequestController:__invoke', 'DocRequestController:get', 'DocRequestController:create', 'DocRequestController:edit', 'DocRequestController:destroy',
                        'FinancialAssistanceController:__invoke', 'FinancialAssistanceController:get', 'FinancialAssistanceController:create', 'FinancialAssistanceController:edit', 'FinancialAssistanceController:destroy',
                        'CivilianController:__invoke', 'CivilianController:get', 'CivilianController:create', 'CivilianController:edit', 'CivilianController:destroy',
                        'RtController:__invoke', 'RtController:get', 'RtController:create', 'RtController:edit', 'RtController:destroy',
                        'ProfileImageController:get', 'ProfileImageController:create', 'ProfileImageController:edit', 'ProfileImageController:destroy',
                        'PasswordController:reset',
                        'UserController:__invoke', 'UserController:get', 'UserController:create', 'UserController:edit', 'UserController:destroy',
                        'NewsController:__invoke', 'NewsController:get', 'NewsController:create', 'NewsController:edit', 'NewsController:destroy',
                        'DuesController:__invoke', 'DuesController:get', 'DuesController:create', 'DuesController:edit', 'DuesController:destroy',
                        'DuesPaymentLogController:__invoke', 'DuesPaymentLogController:get', 'DuesPaymentLogController:create', 'DuesPaymentLogController:edit', 'DuesPaymentLogController:destroy',
                        'CertificateController:__invoke', 'CertificateController:get', 'CertificateController:create', 'CertificateController:edit,CertificateController:destroy',
                        (new ReflectionClass(\App\Http\Controllers\SpendingController::class))->getShortName() . ':get', (new ReflectionClass(\App\Http\Controllers\SpendingController::class))->getShortName() . ':create', (new ReflectionClass(\App\Http\Controllers\SpendingController::class))->getShortName() . ':edit', (new ReflectionClass(\App\Http\Controllers\SpendingController::class))->getShortName() . ':destroy',
                    ];
                }
                if ($user->role === 'RW') {
                    $token_abilities = [
                        'ComplaintController:__invoke', 'ComplaintController:get', 'ComplaintController:create', 'ComplaintController:edit', 'ComplaintController:destroy',
                        'ActivityController:__invoke', 'ActivityController:get', 'ActivityController:create', 'ActivityController:edit', 'ActivityController:destroy',
                        'DocumentationController:__invoke', 'DocumentationController:get', 'DocumentationController:create', 'DocumentationController:edit', 'DocumentationController:destroy',
                        'DocRequestController:__invoke', 'DocRequestController:get', 'DocRequestController:create', 'DocRequestController:edit', 'DocRequestController:destroy',
                        'FinancialAssistanceController:__invoke', 'FinancialAssistanceController:get', 'FinancialAssistanceController:create', 'FinancialAssistanceController:edit', 'FinancialAssistanceController:destroy',
                        'CivilianController:__invoke', 'CivilianController:get', 'CivilianController:create', 'CivilianController:edit', 'CivilianController:destroy',
                        'RtController:__invoke', 'RtController:get', 'RtController:create', 'RtController:edit', 'RtController:destroy',
                        'ProfileImageController:get', 'ProfileImageController:create', 'ProfileImageController:edit', 'ProfileImageController:destroy',
                        'PasswordController:reset',
                        'UserController:__invoke', 'UserController:get', 'UserController:create', 'UserController:edit', 'UserController:destroy',
                        'NewsController:__invoke', 'NewsController:get', 'NewsController:create', 'NewsController:edit', 'NewsController:destroy',
                        'DuesController:__invoke', 'DuesController:get', 'DuesController:create', 'DuesController:edit', 'DuesController:destroy',
                        'DuesPaymentLogController:__invoke', 'DuesPaymentLogController:get', 'DuesPaymentLogController:create', 'DuesPaymentLogController:edit', 'DuesPaymentLogController:destroy',
                        'CertificateController:__invoke', 'CertificateController:get', 'CertificateController:create', 'CertificateController:edit,CertificateController:destroy',
                        (new ReflectionClass(\App\Http\Controllers\SpendingController::class))->getShortName() . ':get', (new ReflectionClass(\App\Http\Controllers\SpendingController::class))->getShortName() . ':create', (new ReflectionClass(\App\Http\Controllers\SpendingController::class))->getShortName() . ':edit', (new ReflectionClass(\App\Http\Controllers\SpendingController::class))->getShortName() . ':destroy',
                    ];
                }
                if ($user->role === 'Warga') {
                    $token_abilities = [
                        'ActivityController:get',
                        'ComplaintController:__invoke', 'ComplaintController:get', 'ComplaintController:create',
                        'DocRequestController:__invoke', 'DocRequestController:get', 'DocRequestController:create',
                        'FinancialAssistanceController:__invoke', 'FinancialAssistanceController:get', 'FinancialAssistanceController:create', 'FinancialAssistanceController:edit',
                        'CivilianController:__invoke', 'CivilianController:get', 'CivilianController:create', 'CivilianController:edit',
                        'RtController:__invoke', 'RtController:get',
                        'ProfileImageController:get', 'ProfileImageController:create', 'ProfileImageController:edit', 'ProfileImageController:destroy',
                        'PasswordController:reset',
                        'UserController:__invoke', 'UserController:get', 'UserController:create', 'UserController:edit', 'UserController:destroy',
                        'NewsController:__invoke', 'NewsController:get',
                        'DuesController:__invoke', 'DuesController:get',
                        'DuesPaymentLogController:__invoke', 'DuesPaymentLogController:get',
                        'CertificateController:__invoke', 'CertificateController:get',
                        'RtController:__invoke', 'RtController:get',
                    ];
                }

                $generatedToken = $user->createToken('access_token', $token_abilities, now()->addWeek());
                $cookie = Cookie('token', $generatedToken->plainTextToken, Carbon::now()->addWeek()->getTimestamp(), '/', null, null, false);

                error_log($generatedToken->plainTextToken);

                // if (str_contains($req->header('referer'), "login")) {
                //     return redirect('beranda')->cookie($cookie);
                // }
                if (str_contains($req->header('referer'), "login")) {
                    return Response()
                        ->json([
                            'status' => true,
                            'token' => $generatedToken->plainTextToken,
                            'exp' => now()->addWeek()->timestamp,
                        ])
                        ->cookie($cookie);
                }

                return Response()
                    ->json([
                        'status' => true,
                        'token' => $generatedToken->plainTextToken,
                        'exp' => now()->addWeek()->timestamp,
                    ])
                    ->cookie($cookie);
            }

            $user = Auth::attempt($req->only('username', 'password'));
            // return redirect('beranda');
            return Inertia::render('Auth/Civilian');
        } catch (\Throwable $th) {
            error_log($th);

            if (str_contains($req->url(), 'api')) {
                return Response()->json(['status' => false, 'err' => $this->errMsg], 404);
            }

            return back()->with(['status' => false, 'err' => $this->errMsg]);
        }
    }
}
