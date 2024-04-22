<?php

namespace App\Http\Controllers;

use App\Http\Requests\CivilianRequest;
use App\Http\Requests\Resources\DeleteCiviliant;
use App\Http\Requests\UpdateCivilianRequest;
use App\Models\Civilian;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Sanctum\PersonalAccessToken;

class CivilianController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Auth/Civilian');
    }

    public function get($filter = null): JsonResponse
    {
        $data = null;
        if ($filter) {
            $data = Civilian::with('family.rt_id')->find($filter);
        } else {
            $data = Civilian::with('family.rt_id')->get();
        }

        return Response()->json(['data' => $data], 200);
    }

    public function create(CivilianRequest $req): JsonResponse
    {
        try {
            $data = Civilian::firstOrCreate([
                'nik' => $req->nik,
                'fullName' => $req->fullName,
                'birthplace' => $req->birthplace,
                'birthdate' => $req->birthdate,
                'residentstatus' => $req->residentstatus,
                'family_id' => $req->family_id,
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

    public function edit(UpdateCivilianRequest $req): JsonResponse
    {
        $payload = $req->safe()->collect();

        try {
            $data = Civilian::withTrashed()
                ->find(['id' => $payload->get('id')])
                ->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'nik' => $req->nik,
                        'fullName' => $req->fullName,
                        'birthplace' => $req->birthplace,
                        'birthdate' => $req->birthdate,
                        'residentstatus' => $req->residentstatus,
                        'family_id' => $req->family_id,
                        'updated_by' => Auth::id(),
                    ]);
                } else {
                    $token = $req->bearerToken();
                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->update([
                        'nik' => $req->nik,
                        'fullName' => $req->fullName,
                        'birthplace' => $req->birthplace,
                        'birthdate' => $req->birthdate,
                        'residentstatus' => $req->residentstatus,
                        'family_id' => $req->family_id,
                        'updated_by' => $model->get('id')[0]->id,
                    ]);
                }

                $data->save();

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

    public function destroy(DeleteCiviliant $req): JsonResponse
    {
        $payload = $req->safe()->collect();

        try {
            $data = Civilian::withTrashed()
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
