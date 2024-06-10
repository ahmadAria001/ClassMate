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
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Sanctum\PersonalAccessToken;
use ReflectionClass;

class CivilianController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Auth/Civilian');
    }

    public function viewArchived(Request $request)
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

        if ($pat->cant((new ReflectionClass($this))->getShortName() . ':get')) {
            return abort(404);
        }

        return Inertia::render('ArsipPenduduk');
    }

    public function get($filter = null): JsonResponse
    {
        $data = null;
        if ($filter) {
            $data = Civilian::withoutTrashed()->with('rt_id')->where('id', '=', $filter)->orWhere('nik', '=', $filter)->orWhere('fullName', '=', $filter)->get()->first();
        } else {
            $data = Civilian::withoutTrashed()->with('rt_id')->where('status', '!=', 'pindah')->where('status', '!=', 'Meninggal')->get();
        }

        return Response()->json(['data' => $data], 200);
    }


    public function getCivilanRT($rt_id): JsonResponse
    {
        $data = Civilian::withoutTrashed()
            ->where('rt_id', $rt_id)
            ->get();

        $length = $data->count();

        return Response()->json(['data' => $data, 'lenght' => $length], 200);
    }

    public function getFamilyHead($rt_id): JsonResponse
    {
        $data = Civilian::withoutTrashed()
            ->with('rt_id')
            ->whereHas('rt_id', function ($q) use ($rt_id) {
                return $q->where('id', $rt_id);
            })
            ->where('isFamilyHead', true)
            ->get();

        $length = $data->count();

        return Response()->json(['data' => $data, 'lenght' => $length], 200);
    }

    public function getCustom($column, $operator, $value): JsonResponse
    {
        $data = Civilian::withoutTrashed()->with('rt_id')->where($column, $operator, $value)->get();

        return Response()->json(['data' => $data], 200);
    }

    public function getArchived($filter = null, $byRT = false): JsonResponse
    {
        $data = null;
        $filter = $filter == 'null' ? null : $filter;
        $byRT = filter_var($byRT, FILTER_VALIDATE_BOOLEAN);

        if (!$filter) {
            $data = Civilian::withTrashed()->with('rt_id')->where('status', '!=', 'Aktif')->get();
        } else {
            if ($byRT) {
                $data = Civilian::withTrashed()
                    ->with('rt_id')
                    ->where('status', '!=', 'Aktif')
                    ->whereHas('rt_id', function ($query) use ($filter) {
                        $query->where('id', $filter);
                    })
                    ->get();
            } else {
                $data = Civilian::withTrashed()->with('rt_id')->where('status', '!=', 'Aktif')->where('id', $filter)->get()->first();
            }
        }

        return Response()->json($data, 200);
    }

    public function create(Create $req): JsonResponse
    {
        $payload = $req->safe()->collect();

        try {
            $headExistance = Civilian::withTrashed()->where('nkk', $payload->get('nkk'))->where('isFamilyHead', true)->first();

            if ($payload->get('isFamilyHead') && $headExistance) {
                if ($headExistance) {
                    $headExistance->isFamilyHead = false;
                    $headExistance->save();
                }
            }

            $existingNik = Civilian::withTrashed()->where('nik', $payload->get('nik'))->first();

            if ($existingNik) {
                return Response()->json(
                    [
                        'status' => false,
                        'message' => 'Data already exist',
                    ],
                    400,
                );
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
                'phone' => preg_replace('/[^0-9]/', '', $payload->get('phone')),
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

    public function edit(Update $req): JsonResponse
    {
        $payload = $req->safe()->collect();

        try {
            $headExistance = Civilian::withTrashed()->where('nkk', '=', $payload->get('nkk'))->where('isFamilyHead', true)->first();

            $data = Civilian::withTrashed()
                ->find(['id' => $payload->get('id')])
                ->first();

            if (!$payload->get('isFamilyHead') && !$headExistance) {
                $this->replaceFamHead($payload->get('nkk'), false);
            } elseif ($payload->get('isFamilyHead') && $headExistance) {
                $this->replaceFamHead($payload->get('nkk'), true);
            }

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
                        'phone' => preg_replace('/[^0-9]/', '', $payload->get('phone')),
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
                        'phone' => preg_replace('/[^0-9]/', '', $payload->get('phone')),
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

            $isFamhead = false;
            $nkk = null;

            if ($data) {
                $isFamhead = $data->isFamilyHead;
                $nkk = $data->nkk;

                if (Auth::guard('web')->check()) {
                    $data->update([
                        'status' => $payload->get('status'),
                        'isFamilyHead' => $data->isFamilyHead ? false : $data->isFamilyHead,
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
                        'isFamilyHead' => $data->isFamilyHead ? false : $data->isFamilyHead,
                        'deleted_by' => $model->get('id')[0]->id,
                    ]);
                }

                $data->save();
                $data->delete();

                if ($isFamhead) {
                    $this->replaceFamHead($nkk, false);
                }

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

    function replaceFamHead(string $nkk, bool $replace)
    {
        try {
            if (!$replace) {
                $headReplacement = Civilian::withTrashed()->where('nkk', '=', $nkk)->where('status', '=', 'Aktif')->orderBy('birthdate')->first();

                if (!$headReplacement) {
                    return;
                }

                $headReplacement->update(['isFamilyHead' => 1]);
                $headReplacement->save();
                return;
            }

            $oldHead = Civilian::withTrashed()->where('nkk', '=', $nkk)->where('status', '=', 'Aktif')->where('isFamilyHead', '=', 1)->first();

            if (!$oldHead) {
                return;
            }

            $oldHead->isFamilyHead = false;
            $oldHead->save();

            error_log('REPLACE TRUE');
            error_log($oldHead->address . $oldHead->residentstatus . $oldHead->isFamilyHead);
            return;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
        }
    }
}
