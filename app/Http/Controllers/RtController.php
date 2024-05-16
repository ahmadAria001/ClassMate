<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\RT\Create;
use App\Http\Requests\Resources\RT\Delete;
use App\Http\Requests\Resources\RT\Update;
use App\Models\RT;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;

class RtController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Auth/RT');
    }

    public function get($filter = null)
    {
        $data = null;

        if ($filter) {
            $data = RT::withoutTrashed()
                ->with('leader_id.civilian_id')
                ->with([
                    'civils' => function ($q) {
                        $q->orderBy('nkk');
                    },
                ])
                ->where('id', '=', $filter)
                ->get();

            if ($data) {
                $data = $data->skip(0)->take(10);
            }
        } else {
            $data = RT::withoutTrashed()
                ->with('leader_id.civilian_id')
                ->with([
                    'civils' => function ($q) {
                        $q->orderBy('nkk');
                    },
                ])
                ->get();

            if ($data) {
                $data = $data->skip(0)->take(10);
            }
        }

        return Response()->json(['data' => $data], 200);
    }

    public function withCivils($filter = null)
    {
        $data = null;

        if ($filter) {
            $data = RT::withTrashed()
                ->with(['civils' => fn($query) => $query->orderBy('nkk')])
                ->where('id', '=', $filter)
                ->skip(0)
                ->take(10)
                ->get();

            if ($data) {
                $data = $data->skip(0)->take(10);
            }
        } else {
            $data = RT::withTrashed()
                ->with([
                    'civils' => function ($query) {
                        $query->orderBy('nkk');
                    },
                ])
                ->skip(0)
                ->take(10)
                ->get();

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
            $data = RT::withTrashed()->whereNull($column)->with('leader_id.civilian_id')->where('number', '>', 0)->get();
        } else {
            $data = RT::withTrashed()->with('leader_id.civilian_id')->where($column, $operator, $value)->get();
        }

        return Response()->json(['data' => $data], 200);
    }

    public function create(Create $req)
    {
        $payload = $req->safe()->collect();

        try {
            $existLeader = RT::withoutTrashed()
                ->where([
                    'leader_id' => $payload->get('leader_id'),
                ])
                ->get();

            if (count($existLeader) > 0) {
                return Response()->json(
                    [
                        'status' => false,
                        'message' => 'Data already exist',
                    ],
                    400,
                );
            }

            $existNumber = RT::withoutTrashed()
                ->where([
                    'number' => $payload->get('number'),
                ])
                ->get();

            if (count($existNumber) > 0) {
                return Response()->json(
                    [
                        'status' => false,
                        'message' => 'Data already exist',
                    ],
                    400,
                );
            }

            $data = RT::firstOrCreate([
                'leader_id' => $payload->get('leader_id'),
                'number' => $payload->get('number'),
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
            $data = RT::withTrashed()
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
            $data = RT::withTrashed()
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
