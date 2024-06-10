<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Certificate\Create;
use App\Http\Requests\Resources\Certificate\Delete;
use App\Http\Requests\Resources\Certificate\Update;
use App\Models\Certificate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;

class CertificateController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Auth/Certificate');
    }

    public function get($filter = null)
    {
        $data = null;

        if ($filter) {
            $data = Certificate::withoutTrashed()->find($filter);
        } else {
            $data = Certificate::withoutTrashed()->all();
        }

        return Response()->json(['data' => $data], 200);
    }

    public function create(Create $req)
    {
        $payload = $req->safe()->collect();
        try {
            $data = Certificate::firstOrCreate([
                'request_by' => $payload->get('request_by'),
                'desc' => $payload->get('desc'),
            ]);

            if ($data->wasRecentlyCreated) {
                $data->created_at = Carbon::now()->timestamp;

                if (str_contains($req->url(), 'api')) {
                    $token = $req->bearerToken();
                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                        if (!$token) {
                            return Response()->json(['message' => 'Unauthorized'], 401);
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->created_by = $model->get('id')[0]->id;
                } else {
                    $data->created_by = Auth::id();
                }

                $data->save();

                return Response()->json([
                    'status' => true,
                    'message' => 'Data Created',
                ]);
            }

            return Response()->json(
                [
                    'status' => false,
                    'message' => 'Data already exist',
                ],
                400,
            );
        } catch (\Throwable $th) {
            error_log($th);
        }
    }

    public function edit(Update $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = Certificate::withTrashed()
                ->find(['id' => $payload->get('id')])
                ->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'typeDues' => $payload->get('typeDues'),
                        'description' => $payload->get('description'),
                        'amt_dues' => $payload->get('amt_dues'),
                        'amt_fund' => $payload->get('amt_fund'),
                        'status' => $payload->get('status'),
                        'rt_id' => $payload->get('rt_id'),
                        'updated_by' => Auth::id(),
                    ]);
                } else {
                    $token = $req->bearerToken();
                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                        if (!$token) {
                            return Response()->json(['message' => 'Unauthorized'], 401);
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->update([
                        'typeDues' => $payload->get('typeDues'),
                        'description' => $payload->get('description'),
                        'amt_dues' => $payload->get('amt_dues'),
                        'amt_fund' => $payload->get('amt_fund'),
                        'status' => $payload->get('status'),
                        'rt_id' => $payload->get('rt_id'),
                        'updated_by' => $model->get('id')[0]->id,
                    ]);
                }

                // $data->save();

                return Response()->json([
                    'status' => true,
                    'message' => 'Data Updated',
                ]);
            }

            return Response()->json(
                [
                    'status' => false,
                    'message' => 'Data Not found',
                ],
                400,
            );
        } catch (\Throwable $th) {
            error_log($th);
        }
    }

    public function destroy(Delete $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = Certificate::withTrashed()
                ->find(['id' => $payload->get('id')])
                ->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'deleted_by' => Auth::id(),
                    ]);
                } else {
                    $token = $req->bearerToken();
                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                        if (!$token) {
                            return Response()->json(['message' => 'Unauthorized'], 401);
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();
                    $data->update([
                        'deleted_by' => $model->get('id')[0]->id,
                    ]);
                }

                $data->save();

                $data->delete();

                return Response()->json([
                    'status' => true,
                    'message' => 'Data Deleted',
                ]);
            }

            return Response()->json(
                [
                    'status' => false,
                    'message' => 'Data Not found',
                ],
                400,
            );
        } catch (\Throwable $th) {
            error_log($th);
        }
    }
}
