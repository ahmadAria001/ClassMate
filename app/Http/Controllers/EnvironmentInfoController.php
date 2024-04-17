<?php

namespace App\Http\Controllers;

use App\Models\EnvironmentInfo;
use App\Models\EnvirontmentInfo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EnvironmentInfoControllerInfoController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Auth/RT');
    }

    public function get($filter = null)
    {
        $data = null;

        if ($filter)
            $data = EnvironmentInfo::with('leader_id.civilian_id')
                ->with('family.civil')
                ->find($filter)
                ->skip(0)
                ->take(10)
                ->get();
        else
            $data = EnvironmentInfo::with('leader_id.civilian_id')
                ->with('family.civil')
                ->skip(0)
                ->take(10)
                ->get();

        return Response()->json(['data' => $data], 200);
    }

    public function create(Create $req)
    {
        $payload = $req->safe()->collect();

        try {
            $exist = EnvironmentInfo::find([
                'leader_id' => $payload->get('leader_id'),
            ]);

            if (count($exist) > 0) {
                return Response()->json([
                    'status' => false,
                    'message' => 'Data already exist'
                ], 400);
            }

            $data = EnvirontmentInfo::firstOrCreate([
                'leader_id' => $payload->get('leader_id'),
            ]);

            if ($data->wasRecentlyCreated) {
                $data->created_at = Carbon::now()->timestamp;

                if (str_contains($req->url(), 'api')) {
                    $token = $req->bearerToken();
                    $pat = PersonalAccessToken::findToken($token);

                    $model = $pat->tokenable();

                    $data->created_by = ($model->get('id'))[0]->id;
                } else
                    $data->created_by = Auth::id();

                $data->save();

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
        }
    }

    public function edit(Update $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = RT::withTrashed()->find(['id' => $payload->get('id')])->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'leader_id' => $payload->get('leader_id'),
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    $token = $req->bearerToken();
                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->update([
                        'leader_id' => $payload->get('leader_id'),
                        'updated_by' => ($model->get('id'))[0]->id
                    ]);
                }

                return Response()->json([
                    'status' => true,
                    'message' => 'Data Updated'
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

    public function destroy(Delete $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = RT::withTrashed()->find(['id' => $payload->get('id')])->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'deleted_by' => Auth::id()
                    ]);
                } else {
                    $token = $req->bearerToken();
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
