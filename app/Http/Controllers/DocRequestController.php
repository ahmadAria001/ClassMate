<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Docs\CreateRequestDoc;
use App\Http\Requests\Resources\Docs\DeleteRequestDoc;
use App\Http\Requests\Resources\Docs\UpdateRequestDoc;
use App\Models\DocRequest;
use App\Models\Docs;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;
use ReflectionClass;

class DocRequestController extends Controller
{
    public function __invoke(Request $request)
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

        if ($pat->cant((new ReflectionClass($this))->getShortName() . ':__invoke')) {
            return abort(404);
        }

        return Inertia::render('StatusPengajuan');
    }

    public function manageReqView(Request $request)
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

        return Inertia::render('DaftarPermintaanSurat');
    }

    public function get($filter = null)
    {
        $data = null;
        if ($filter) {
            $data = DocRequest::withoutTrashed()->with('created_by.civilian_id', 'updated_by', 'docs_id', 'request_by')->find($filter);
        } else {
            $data = DocRequest::withoutTrashed()->with('created_by.civilian_id', 'updated_by', 'docs_id', 'request_by')->get();
        }

        return response()->json(['data' => $data], 200);
    }

    public function getPaged($page = 1)
    {
        $take = 5;

        $data = DocRequest::withoutTrashed()
            ->with('created_by.civilian_id', 'updated_by', 'docs_id', 'request_by')
            ->skip($page > 1 ? ($page - 1) * $take : 0)
            ->take($take)
            ->get();

        $length = DocRequest::withoutTrashed()->count();

        return response()->json(['data' => $data, 'length' => $length]);
    }

    public function getForRT(Request $req, $page = 1)
    {
        $take = 5;
        $identity = $this->getToken($req);

        if (!$req) abort(401);
        $model = $identity->tokenable();
        $user = User::withoutTrashed()->with('civilian_id.rt_id')->where('id', $model->get('id')[0]->id)->get()->first();

        $data = DocRequest::withoutTrashed()
            ->with('created_by.civilian_id', 'updated_by', 'docs_id', 'request_by')
            ->whereHas('request_by.rt_id', function ($q) use ($user) {
                $q->where(
                    'id',
                    $user->getRelation('civilian_id')->id
                );
            })
            ->skip($page > 1 ? ($page - 1) * $take : 0)
            ->take($take)
            ->get();

        $length = $data->count();

        return response()->json(['data' => $data, 'length' => $length]);
    }

    public function getForRW($page = 1)
    {
        $take = 5;

        $data = DocRequest::withoutTrashed()
            ->with('created_by.civilian_id', 'updated_by', 'docs_id', 'request_by')
            ->whereNot('responsed_by_rt')
            ->skip($page > 1 ? ($page - 1) * $take : 0)
            ->take($take)
            ->get();

        $length = $data->count();

        return response()->json(['data' => $data, 'length' => $length]);
    }

    public function getByWarga(Request $req, $page = 1)
    {
        $take = 5;
        $identity = $this->getToken($req);

        if (!$req) abort(401);
        $model = $identity->tokenable();

        $data = DocRequest::withoutTrashed()
            ->with('created_by.civilian_id', 'updated_by', 'docs_id', 'request_by')
            ->where('request_by', '=', $model->get('id')[0]->id)
            ->skip($page > 1 ? ($page - 1) * $take : 0)
            ->take($take)
            ->get();

        $length = $data->count();

        return response()->json(['data' => $data, 'length' => $length]);
    }

    public function create(CreateRequestDoc $request)
    {
        $payload = $request->safe()->collect();

        try {
            $docs = Docs::create([
                'description' => $payload->get('description'),
                'type' => 'Request',
            ]);

            if ($docs->wasRecentlyCreated) {
                $docs->created_at = Carbon::now()->timestamp;

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

                    $data = DocRequest::create([
                        'docs_id' => $docs->id,
                        'request_by' => $payload->get('request_by'),
                        'created_at' => Carbon::now()->timestamp,
                    ]);

                    $docs->created_by = $model->get('id')[0]->id;
                    $data->created_by = $model->get('id')[0]->id;
                } else {
                    $data = DocRequest::create([
                        'docs_id' => $docs->id,
                        'request_by' => $payload->get('request_by'),
                        'created_at' => Carbon::now()->timestamp,
                    ]);

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
    public function edit(UpdateRequestDoc $request)
    {
        $payload = $request->safe()->collect();

        try {
            $data = DocRequest::withTrashed()->where('id', $payload->get('id'))->first();
            $docs = Docs::withTrashed()
                ->where('id', $data->docs_id)
                ->first();

            if ($data) {
                $approved = 'Open';

                $approval_rt = $payload->get('rw_stat');
                $approval_rw = $payload->get('rt_stat');
                if ($approval_rt == 1 && $approval_rw == 1) {
                    $approved = 'Resolved';
                } else if ($approval_rt == 2 || $approval_rw == 2) {
                    $approved = 'Open';
                } else if ($approval_rt == 1 && $approval_rw == 1) {
                    $approved = 'Declined';
                }

                //Handle Log Update
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'responsed_by_rt' => $payload->get('responsed_by_rt') ? $payload->get('responsed_by_rt') : $data->responsed_by_rt,
                        'rt_stat' => $payload->get('rt_stat') ? $payload->get('rt_stat') : $data->rt_stat,
                        'responsed_by_rw' => $payload->get('responsed_by_rw') ? $payload->get('responsed_by_rw') : $data->responsed_by_rw,
                        'rw_stat' => $payload->get('rw_stat') ? $payload->get('rw_stat') : $data->rw_stat,
                        'requestStatus' => $approved,
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
                        'requestStatus' => $approved,
                        'responsed_by_rt' => $payload->get('responsed_by_rt') ? $payload->get('responsed_by_rt') : $data->responsed_by_rt,
                        'rt_stat' => $payload->get('rt_stat') ? $payload->get('rt_stat') : $data->rt_stat,
                        'responsed_by_rw' => $payload->get('responsed_by_rw') ? $payload->get('responsed_by_rw') : $data->responsed_by_rw,
                        'rw_stat' => $payload->get('rw_stat') ? $payload->get('rw_stat') : $data->rw_stat,
                        'updated_by' => $model->get('id')[0]->id,
                    ]);

                    $docs->update([
                        'description' => $payload->get('description') ? $payload->get('description') : $docs->description,
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
    public function destroy(DeleteRequestDoc $request)
    {
        $payload = $request->safe()->collect();

        try {
            $data = DocRequest::withTrashed()->where('id', $payload->get('id'))->first();
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

    private function getToken(Request $request): PersonalAccessToken|null
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

        return PersonalAccessToken::findToken($token);
    }
}
