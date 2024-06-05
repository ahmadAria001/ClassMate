<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\RT\Create;
use App\Http\Requests\Resources\RT\Delete;
use App\Http\Requests\Resources\RT\Update;
use App\Models\RT;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;
use ReflectionClass;

class RtController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Auth/RT');
    }

    public function manageRTView(Request $request)
    {
        $token = null;
        if (str_contains($request->url(), 'api')) {
            $token = $request->bearerToken();
            if (!$token) {
                $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                if (!$token) {
                    return redirect('login');
                }
            }
        } else {
            $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

            if (!$token) {
                return redirect('login');
            }
        }

        $pat = PersonalAccessToken::findToken($token);

        if ($pat->cant((new ReflectionClass($this))->getShortName() . ':create') && $pat->cant((new ReflectionClass($this))->getShortName() . ':edit') && $pat->cant((new ReflectionClass($this))->getShortName() . ':destroy')) {
            return abort(404);
        }

        return Inertia::render('DaftarRT');
    }

    public function civilianBuilder(Request $request)
    {
        $token = null;
        if (str_contains($request->url(), 'api')) {
            $token = $request->bearerToken();
            if (!$token) {
                $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                if (!$token) {
                    return redirect('login');
                }
            }
        } else {
            $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

            if (!$token) {
                return redirect('login');
            }
        }

        $pat = PersonalAccessToken::findToken($token);

        if ($pat->cant((new ReflectionClass($this))->getShortName() . ':create') && $pat->cant((new ReflectionClass($this))->getShortName() . ':edit') && $pat->cant((new ReflectionClass($this))->getShortName() . ':destroy')) {
            return abort(404);
        }

        return Inertia::render('PendudukByRT');
    }

    public function get($page = 1, $filter = null)
    {
        $data = null;
        $take = 5;
        $length = 0;

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
                $data = $data->skip(0)->take(100);
            }
        } else {
            $data = RT::withoutTrashed()
                ->with('leader_id.civilian_id')
                ->with([
                    'civils' => function ($q) {
                        $q->orderBy('nkk');
                    },
                ])
                ->skip($page > 1 ? ($page - 1) * $take : 0)
                ->take($page == 0 ? 100 : $take)
                ->get();
            $length = RT::withoutTrashed()->count();

            if ($data) {
                $data = $data->skip(0)->take(10);
            }
        }

        return Response()->json(['data' => $data, 'length' => $length], 200);
    }

    public function getDropDown()
    {
        $data = RT::withoutTrashed()
            ->get(['id', 'number']);
        $length = RT::withoutTrashed()->count();

        return Response()->json(['data' => $data, 'length' => $length], 200);
    }

    public function withCivils($page = 1, $filter = null)
    {
        $data = null;
        $take = 5;

        if ($filter) {
            $data = RT::withoutTrashed()
                ->with(['civils' => fn ($query) => $query->orderBy('nkk')])
                ->where('id', '=', $filter)
                ->skip($page > 1 ? ($page - 1) * $take : 0)
                ->take($take)
                ->get();
        } else {
            $data = RT::withoutTrashed()
                ->with([
                    'civils' => function ($query) {
                        $query->orderBy('nkk');
                    },
                ])
                ->skip($page > 1 ? ($page - 1) * $take : 0)
                ->take($take)

                ->get();

            if ($data) {
                $data = $data->skip(0)->take(10);
            }
        }

        $length = $data->count();

        return Response()->json(['data' => $data, 'length' => $length], 200);
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

            $old_leader = $data->leader_id;

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'leader_id' => $payload->get('leader_id'),
                        'number' => $payload->get('number'),
                        'updated_by' => Auth::id(),
                    ]);

                    $leader = User::withoutTrashed()->where('id', $payload->get('leader_id'))->first();
                    $leader->update([
                        'role' => 'RT',
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

                    $leader = User::withoutTrashed()->where('id', $payload->get('leader_id'))->first();
                    $leader->update([
                        'role' => 'RT',
                        'updated_by' => $model->get('id')[0]->id,
                    ]);
                }

                if ($old_leader) {
                    $formerLeader = User::withoutTrashed()->where('id', $old_leader)->first();
                    $formerLeader->update([
                        'role' => 'Warga',
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
