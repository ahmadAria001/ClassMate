<?php

namespace App\Http\Controllers;

use App\Http\Requests\CivilianRequest;
use App\Models\Civilian;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Sanctum\PersonalAccessToken;

class CivilianController extends Controller
{
    public function __invoke(): Response
    {
        $data = Civilian::getAll();
        return Inertia::render('index', ['data' => $data]);
    }

    public function getAll(): JsonResponse
    {
        $data = Civilian::all();
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

                    $data->created_by = ($model->get('id'))[0]->id;
                    // error_log(($model->get('id'))[0]->id);
                }

                $data->created_by = 1;

                $data->save();

                return Response()->json([
                    'status' => true,
                    'message' => 'Data Created'
                ]);
            } else {
                $token = $req->bearerToken();
                $pat = PersonalAccessToken::findToken($token);
                $model = $pat->tokenable();
                error_log(($model->get('id'))[0]->id);

                return Response()->json([
                    'status' => false,
                    'message' => 'Data already exist'
                ]);
            }
        } catch (\Throwable $th) {
            error_log($th);
        }
    }
}
