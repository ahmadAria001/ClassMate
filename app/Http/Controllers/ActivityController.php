<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Docs\CreateActivity;
use App\Http\Requests\Resources\Docs\DeleteActivity;
use App\Http\Requests\Resources\Docs\UpdateActivity;
use App\Models\Activity;
use App\Models\Docs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;
use ReflectionClass;

class ActivityController extends Controller
{
    public function __invoke()
    {
        return Inertia::render();
    }

    public function manageActView(Request $request)
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

        return Inertia::render('KegiatanWarga');
    }

    public function get($filter = null)
    {
        $data = null;

        if ($filter) {
            $data = Activity::with('docs_id', 'created_by.civilian_id.rt_id', 'updated_by')->find($filter);
        } else {
            $data = Activity::with('docs_id', 'created_by.civilian_id.rt_id', 'updated_by')->get();
        }

        return Response()->json(['data' => $data], 200);
    }

    public function getlts()
    {
        $data = Activity::with('docs_id')->orderByDesc('startDate')->take(3)->get();
        $length = $data->count();

        return response()->json(['data' => $data, 'length' => $length]);
    }

    public function getPaged($page = 1)
    {
        $take = 5;

        $data = Activity::withoutTrashed()
            ->with('docs_id', 'created_by.civilian_id.rt_id', 'updated_by')
            ->orderByDesc('startDate')
            ->skip($page > 1 ? ($page - 1) * $take : 0)
            ->take($take)
            ->get();

        $length = Activity::withoutTrashed()->count();

        return response()->json(['data' => $data, 'length' => $length]);
    }

    public function getLike($page = 1, $filter = null)
    {
        $take = 5;

        $data = Activity::withoutTrashed()
            ->with('docs_id', 'created_by.civilian_id.rt_id', 'updated_by')
            ->whereAny(['name', 'location'], 'LIKE', "%$filter%")
            ->orderByDesc('created_at')
            ->skip($page > 1 ? ($page - 1) * $take : 0)
            ->take($take)
            ->get();

        $length = Activity::withoutTrashed()
            ->with('docs_id', 'created_by.civilian_id.rt_id', 'updated_by')
            ->whereAny(['name', 'location'], 'LIKE', "%$filter%")
            ->get()
            ->count();

        return response()->json(['data' => $data, 'length' => $length]);
    }

    public function create(CreateActivity $request)
    {
        $payload = $request->safe()->collect();

        try {
            $docs = Docs::create([
                'description' => $payload->get('description'),
                'type' => 'Event',
            ]);
            $data = Activity::create([
                'docs_id' => $docs->id,
                'name' => $payload->get('name'),
                'startDate' => strtotime($payload->get('startDate')),
                'endDate' => strtotime($payload->get('endDate')),
                'location' => $payload->get('location'),
            ]);

            if ($data->wasRecentlyCreated) {
                $docs->created_at = Carbon::now()->timestamp;
                $data->created_at = Carbon::now()->timestamp;

                if (str_contains($request->url(), 'api')) {
                    $token = $request->bearerToken();
                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                        if (!$token) {
                            return Response()->json(['message' => 'Unauthorized'], 401);
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $docs->created_by = $model->get('id')[0]->id;
                    $data->created_by = $model->get('id')[0]->id;
                } else {
                    $docs->created_by = Auth::id();
                    $data->created_by = Auth::id();
                }
                $docs->save();
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
    public function edit(UpdateActivity $request)
    {
        $payload = $request->safe()->collect();

        try {
            $data = Activity::withTrashed()->where('id', $payload->get('id'))->first();
            $docs = Docs::withTrashed()
                ->where('id', $data->docs_id)
                ->first();
            if ($data) {
                //Handle Log Update
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'name' => $payload->has('name') ? $payload->get('name') : $data->endDate,
                        'startDate' => $payload->has('startDate') ? strtotime($payload->get('startDate')) : $data->startDate,
                        'endDate' => $payload->has('endDate') ? strtotime($payload->get('endDate')) : $data->endDate,
                        'location' => $payload->has('location') ? $payload->get('location') : $data->location,
                        'updated_by' => Auth::id(),
                    ]);

                    $docs->update([
                        'description' => $payload->has('description') ? $payload->get('description') : $docs->description,
                        'updated_by' => Auth::id(),
                    ]);
                } else {
                    $token = $request->bearerToken();
                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                        if (!$token) {
                            return Response()->json(['message' => 'Unauthorized'], 401);
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->update([
                        'name' => $payload->has('name') ? $payload->get('name') : $data->endDate,
                        'startDate' => $payload->has('startDate') ? strtotime($payload->get('startDate')) : $data->startDate,
                        'endDate' => $payload->has('endDate') ? strtotime($payload->get('endDate')) : $data->endDate,
                        'location' => $payload->has('location') ? $payload->get('location') : $data->location,
                        'updated_by' => $model->get('id')[0]->id,
                    ]);

                    $docs->update([
                        'description' => $payload->has('description') ? $payload->get('description') : $docs->description,
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
        } catch (\throwable $th) {
            error_log($th);
        }
    }
    public function destroy(DeleteActivity $request)
    {
        $payload = $request->safe()->collect();

        try {
            $data = Activity::withTrashed()->where('id', $payload->get('id'))->first();
            $docs = Docs::withTrashed()
                ->where('id', $data->docs_id)
                ->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'deleted_by' => Auth::id(),
                    ]);
                } else {
                    $token = $request->bearerToken();
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
                    $docs->update([
                        'deleted_by' => $model->get('id')[0]->id,
                    ]);
                }
                $data->delete();
                $docs->delete();
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
