<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\User\Create;
use App\Http\Requests\Resources\User\Delete;
use App\Http\Requests\Resources\User\Update;
use App\Models\Civilian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function get($filter = null)
    {
        $data = null;

        if ($filter) {
            $data = User::withoutTrashed()->with('civilian_id.rt_id')->where('id', '=', $filter)->get();

            if ($data) {
                $data = $data->skip(0)->take(10);
            }
        } else {
            $data = User::withoutTrashed()->with('civilian_id.rt_id')->get();

            if ($data) {
                $data = $data->skip(0)->take(10);
            }
        }

        return Response()->json(['data' => $data], 200);
    }

    public function getCustom($column, $operator, $value)
    {
        $data = null;
        if ($value == 'null') {
            $data = User::withoutTrashed()->whereNull($column)->with('civilian_id.rt_id')->where('number', '>', 0)->get();
        } else {
            $data = User::withoutTrashed()->with('civilian_id.rt_id')->where($column, $operator, $value)->get();
        }

        return Response()->json(['data' => $data], 200);
    }

    public function create(Create $req)
    {
        $payload = $req->safe()->collect();

        try {
            $existingCivils = Civilian::where('nik', '=', $payload->get('nik'))->first();

            if (!$existingCivils) {
                return Response()->json(
                    [
                        'status' => false,
                        'message' => 'Warga tidak ditemukan',
                    ],
                    400,
                );
            }

            $username = preg_replace('/\s+/', '', strtolower($existingCivils->fullName));
            $existingName = User::where('username', '=', $username)->get();

            if (count($existingName) > 0) {
                $username = $username . count($existingName);
            }

            $data = User::firstOrCreate([
                'username' => $username,
                'password' => Hash::make($payload->get('password')),
                'role' => 'Warga',
                'civilian_id' => $existingCivils->id,
            ]);

            if ($data->wasRecentlyCreated) {
                $data->created_at = Carbon::now()->timestamp;

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
            $data = User::withTrashed()
                ->find(['id' => $payload->get('id')])
                ->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'leader_id' => $payload->get('leader_id'),
                        'number' => $payload->get('number'),
                        'updated_by' => Auth::id(),
                    ]);
                } else {
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
                    $model = $pat->tokenable();

                    $data->update([
                        'leader_id' => $payload->get('leader_id'),
                        'number' => $payload->get('number'),
                        'updated_by' => $model->get('id')[0]->id,
                    ]);
                }

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
            $data = User::withTrashed()
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
                            return Response()->json(
                                [
                                    'message' => 'Unauthorized',
                                ],
                                401,
                            );
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
