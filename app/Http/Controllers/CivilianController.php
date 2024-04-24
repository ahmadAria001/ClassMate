<?php

namespace App\Http\Controllers;

use App\Http\Requests\CivilianRequest;
use App\Http\Requests\Resources\Civilian\Create;
use App\Http\Requests\Resources\Civilian\Delete;
use App\Http\Requests\Resources\Civilian\Update;
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
            $data = Civilian::with('rt_id')->find($filter);
        } else {
            $data = Civilian::with('rt_id')
                ->where(['status', 'status'], ['!=', '!='], ['Meninggal', 'pindah'], 'or')
                ->get();
        }

        return Response()->json(['data' => $data], 200);
    }

    public function getCustom($column, $operator, $value): JsonResponse
    {
        $data = Civilian::with('rt_id')->where($column, $operator, $value)->get();
        return Response()->json(['data' => $data], 200);
    }

    public function create(Create $req): JsonResponse
    {
        $payload = $req->safe()->collect();

        try {
            $existingHead = null;

            if ($payload->get('status')) {
                $existingHead = Civilian::withTrashed()->where('nkk', $payload->get('nkk'))->where('isFamilyHead', true)->first();

                if ($existingHead) {
                    $existingHead->isFamilyHead = false;
                    $existingHead->save();
                }
            }

            $data = Civilian::firstOrCreate([
                'nik' => $payload->get('nik'),
                'fullName' => $payload->get('fullName'),
                'birthplace' => $payload->get('birthplace'),
                'birthdate' => $payload->get('birthdate'),
                'residentstatus' => $payload->get('residentstatus'),
                'nkk' => $payload->get('nkk'),
                'isFamilyHead' => $payload->get('isFamilyHead'),
                'rt_id' => $payload->get('rt_id'),
                'address' => $payload->get('address'),
                'status' => $payload->get('status'),
                'phone' => preg_replace('/[^0-9]/', ' ', $payload->get('phone')),
                'religion' => $payload->get('religion'),
                'job' => $payload->get('job'),
            ]);

            if ($data->wasRecentlyCreated) {
                $data->created_at = Carbon::now()->timestamp;

                if (str_contains($req->url(), 'api')) {
                    $token = $req->bearerToken();

                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

                        if (!$token) {
                            return null;
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

    public function edit(Update $req): JsonResponse
    {
        $payload = $req->safe()->collect();

        try {
            $data = Civilian::withTrashed()
                ->find(['id' => $payload->get('id')])
                ->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'nik' => $payload->get('nik'),
                        'fullName' => $payload->get('fullName'),
                        'birthplace' => $payload->get('birthplace'),
                        'birthdate' => $payload->get('birthdate'),
                        'residentstatus' => $payload->get('residentstatus'),
                        'nkk' => $payload->get('nkk'),
                        'isFamilyHead' => $payload->get('isFamilyHead'),
                        'rt_id' => $payload->get('rt_id'),
                        'address' => $payload->get('address'),
                        'status' => $payload->get('status'),
                        'phone' => preg_replace('/[^0-9]/', ' ', $payload->get('phone')),
                        'religion' => $payload->get('religion'),
                        'job' => $payload->get('job'),
                        'updated_by' => Auth::id(),
                    ]);
                } else {
                    $token = $req->bearerToken();
                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

                        if (!$token) {
                            return null;
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->update([
                        'nik' => $payload->get('nik'),
                        'fullName' => $payload->get('fullName'),
                        'birthplace' => $payload->get('birthplace'),
                        'birthdate' => $payload->get('birthdate'),
                        'residentstatus' => $payload->get('residentstatus'),
                        'nkk' => $payload->get('nkk'),
                        'isFamilyHead' => $payload->get('isFamilyHead'),
                        'rt_id' => $payload->get('rt_id'),
                        'address' => $payload->get('address'),
                        'status' => $payload->get('status'),
                        'phone' => preg_replace('/[^0-9]/', ' ', $payload->get('phone')),
                        'religion' => $payload->get('religion'),
                        'job' => $payload->get('job'),
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

    public function destroy(Delete $req): JsonResponse
    {
        $payload = $req->safe()->collect();

        try {
            $data = Civilian::withTrashed()
                ->find(['id' => $payload->get('id')])
                ->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'status' => $payload->get('status'),
                        'deleted_by' => Auth::id(),
                    ]);
                } else {
                    $token = $req->bearerToken();
                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

                        if (!$token) {
                            return null;
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->update([
                        'status' => $payload->get('status'),
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
