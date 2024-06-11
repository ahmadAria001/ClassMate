<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Environment\Create;
use App\Http\Requests\Resources\Environment\Delete;
use App\Http\Requests\Resources\Environment\Update;
use App\Models\EnvironmentInfo;
use App\Models\RT;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;

class EnvironmentInfoController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Auth/RT');
    }

    public function get($filter = null)
    {
        $data = null;

        if ($filter)
            $data = EnvironmentInfo::where('created_by', $filter)
                ->skip(0)
                ->take(10)
                ->get();
        else
            $data = EnvironmentInfo::skip(0)
                ->take(10)
                ->get();

        return Response()->json(['data' => $data], 200);
    }

    public function getlts(){

        // $data = null;

        $data = EnvironmentInfo::orderByDesc('created_at')->get()->first();
        // $data = EnvironmentInfo::all();

        // echo var_dump($data);

        return Response()->json(['data' => $data], 200);
    }

    public function create(Create $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = EnvironmentInfo::Create([
                'env_condition' => $payload->get('env_condition'),
                'desc' => $payload->get('desc'),
                'general_facility' => $payload->get('general_facility'),
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
            $data = EnvironmentInfo::withTrashed()->find(['id' => $payload->get('id')])->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'env_condition' => $payload->get('env_condition'),
                        'desc' => $payload->get('desc'),
                        'general_facility' => $payload->get('general_facility'),
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    $token = $req->bearerToken();
                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->update([
                        'env_condition' => $payload->get('env_condition'),
                        'desc' => $payload->get('desc'),
                        'general_facility' => $payload->get('general_facility'),
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
            $data = EnvironmentInfo::withTrashed()->find(['id' => $payload->get('id')])->first();
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
