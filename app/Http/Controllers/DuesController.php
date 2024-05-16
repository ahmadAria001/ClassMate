<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\CreateDues;
use App\Http\Requests\Resources\EditDues;
use App\Http\Requests\Resources\DeleteDues;
use App\Models\Dues;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;

class DuesController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Auth/Dues');
    }

    public function get($filter = null)
    {
        $data = null;

        if ($filter) {
            $data = Dues::withoutTrashed()->with('rt_id')->find($filter);
        } else {
            $data = Dues::withoutTrashed()->with('rt_id')->get();
        }

        return Response()->json(['data' => $data], 200);
    }

    public function create(CreateDues $req)
    {
        $payload = $req->safe()->collect();

        try {
            $exist = Dues::find([
                'typeDues' => $payload->get('typeDues'),
                'rt_id' => $payload->get('rt_id'),
            ]);

            if (count($exist) > 0) {
                return Response()->json(
                    [
                        'status' => false,
                        'message' => 'Data already exist',
                    ],
                    400,
                );
            }

            $data = Dues::firstOrCreate([
                'typeDues' => $payload->get('typeDues'),
                'description' => $payload->get('description'),
                'amt_dues' => $payload->get('amt_dues'),
                'amt_fund' => $payload->get('amt_fund'),
                'status' => $payload->get('status'),
                'rt_id' => $payload->get('rt_id'),
            ]);

            if ($data->wasRecentlyCreated) {
                $data->created_at = Carbon::now()->timestamp;

                if (str_contains($req->url(), 'api')) {
                    $token = $req->bearerToken();
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

    public function edit(EditDues $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = Dues::withTrashed()
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

    public function destroy(DeleteDues $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = Dues::withTrashed()
                ->find(['id' => $payload->get('id')])
                ->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'deleted_by' => Auth::id(),
                    ]);
                } else {
                    $token = $req->bearerToken();
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
