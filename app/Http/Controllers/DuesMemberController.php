<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Dues\Member\Create;
use App\Http\Requests\Resources\Dues\Member\Update;
use App\Http\Requests\Resources\Dues\Member\Delete;
use App\Models\DuesMember;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class DuesMemberController extends Controller
{
    public function create(Create $req)
    {
        $payload = $req->safe()->collect();

        try {

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

                $member = DuesMember::firstOrCreate([
                    'dues' => $payload->get('dues'),
                    'member' => $payload->get('member'),
                    'created_at' => Carbon::now()->timestamp,
                    'created_by' => ($model->get('id'))[0]->id
                ]);
            } else {
                $member = DuesMember::firstOrCreate([
                    'dues' => $payload->get('dues'),
                    'member' => $payload->get('member'),
                    'created_at' => Carbon::now()->timestamp,
                    'created_by' => Auth::id()
                ]);
            }

            if ($member->wasRecentlyCreated) {
                return Response()->json([
                    'status' => true,
                    'message' => 'Data Created'
                ]);
            }

            return Response()->json([
                'status' => false,
                'message' => 'Data already exist'
            ], 400);
        } catch (\Throwable $th) {
            error_log($th);
            return Response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function edit(Update $req)
    {
        $payload = $req->safe()->collect();

        try {

            $data = DuesMember::withoutTrashed()->find($payload->get('id'))->first();

            if (!$data) {
                return Response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

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

                $data->update([
                    'dues' => $payload->get('dues'),
                    'member' => $payload->get('member'),
                    'updated_at' => Carbon::now()->timestamp,
                    'updated_by' => ($model->get('id'))[0]->id
                ]);
            } else {
                $data->update([
                    'dues' => $payload->has('dues') ? $payload->get('dues') : $data->dues,
                    'member' => $payload->has('member') ? $payload->get('member') : $data->member,
                    'updated_at' => Carbon::now()->timestamp,
                    'updated_by' => Auth::id()
                ]);
            }

            return Response()->json([
                'status' => true,
                'message' => 'Data Created'
            ]);
        } catch (\Throwable $th) {
            error_log($th);
            return Response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function destroy(Delete $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = DuesMember::withTrashed()->where('id', $payload->get('id'))->first();
            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'deleted_by' => Auth::id()
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
                        'deleted_by' => ($model->get('id'))[0]->id
                    ]);
                }

                $data->save();

                $data->delete();
                return Response()->json([
                    'status' => true,
                    'message' => 'Data Deleted'
                ]);
            }

            return Response()->json([
                'status' => false,
                'message' => 'Data Not found'
            ], 400);
        } catch (\Throwable $th) {
            error_log($th);
        }
    }
}
