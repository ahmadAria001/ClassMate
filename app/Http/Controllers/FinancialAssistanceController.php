<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\FA\Create;
use App\Http\Requests\Resources\FA\Delete;
use App\Http\Requests\Resources\FA\Update;
use App\Models\FinancialAssistance;
use App\Models\RT;
use App\Models\User;
use App\Utils\AccessToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;
use ReflectionClass;

class FinancialAssistanceController extends Controller
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

        if (!$pat)
            return abort(404);

        if ($pat->cant((new ReflectionClass($this))->getShortName() . ':__invoke')) {
            return abort(404);
        }

        return Inertia::render('StatusBansos');
    }

    public function manageFAView(Request $request)
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

        return Inertia::render('DaftarBansos');
    }

    // #GET
    public function get($filter = null)
    {
        $data = null;

        if ($filter) {
            $data = RT::with(['family.civil.fa'])->find($filter);
        } else {
            $data = RT::with('family.civil.fa')->get();
        }

        return Response()->json(['data' => $data], 200);
    }

    public function getPaged($page = 1)
    {
        $take = 5;

        $data = FinancialAssistance::withoutTrashed()
            ->with('request_by.civilian_id.rt_id', 'created_by.civilian_id', 'updated_by')
            ->get();

        $length = FinancialAssistance::withoutTrashed()->count();

        return response()->json(['data' => $data, 'length' => $length]);
    }

    public function getByWarga(Request $req, $page = 1)
    {
        $data = null;
        $take = 5;
        $identity = AccessToken::getToken($req);

        if (!$req) abort(401);
        $model = $identity->tokenable();
        $user = User::withoutTrashed()->with('civilian_id.rt_id')->where('id', $model->get('id')[0]->id)->get()->first();

        $data = FinancialAssistance::withoutTrashed()
            ->with('request_by.civilian_id.rt_id', 'created_by.civilian_id', 'updated_by')
            ->whereHas('request_by', function ($q) use ($user) {
                $q->where(
                    'id',
                    $user->id
                );
            })
            ->skip($page > 1 ? ($page - 1) * $take : 0)
            ->take($take)
            ->get();

        error_log($data);

        $length = $data->count();

        return response()->json(['data' => $data, 'length' => $length]);
    }

    public function getByRT(Request $req, $page = 1)
    {
        $data = null;
        $take = 5;
        $identity = AccessToken::getToken($req);

        if (!$identity) abort(401);
        $model = $identity->tokenable();
        $user = User::withoutTrashed()->with('civilian_id.rt_id')->where('id', $model->get('id')[0]->id)->get()->first();

        $data = FinancialAssistance::withoutTrashed()
            ->with('request_by.civilian_id.rt_id', 'created_by.civilian_id', 'updated_by')
            ->where('status', '=', 2)
            ->whereHas('request_by.civilian_id', function ($q) use ($user) {
                $q->where(
                    'rt_id',
                    $user->getRelation('civilian_id')->rt_id
                );
            })
            ->get();

        $length = FinancialAssistance::withoutTrashed()->count();

        return response()->json(['data' => $data, 'length' => $length]);
    }

    public function getLike($page = 1, $filter = null)
    {
        $take = 5;

        $data = FinancialAssistance::withoutTrashed()
            ->with('request_by.civilian_id.rt_id', 'created_by.civilian_id', 'updated_by')
            ->whereHas('request_by.civilian_id', function ($q) use ($filter) {
                $q->whereAny(['fullName'], 'LIKE', "%$filter%");
            })
            ->orderByDesc('created_at')
            ->skip($page > 1 ? ($page - 1) * $take : 0)
            ->take($take)
            ->get();

        $length = FinancialAssistance::withoutTrashed()
            ->with('request_by.civilian_id.rt_id', 'created_by.civilian_id', 'updated_by')
            ->whereHas('request_by.civilian_id', function ($q) use ($filter) {
                $q->whereAny(['fullName'], 'LIKE', "%$filter%");
            })
            ->orderByDesc('created_at')
            ->get()
            ->count();

        return response()->json(['data' => $data, 'length' => $length]);
    }

    // #POST
    public function create(Create $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = FinancialAssistance::firstOrCreate([
                'request_by' => $payload->get('request_by'),
                'childrens' => $payload->get('childrens'),
                'salary' => $payload->get('salary'),
                'expenses' => $payload->get('expenses'),
                'job_status' => $payload->get('job_status'),
                'residence_status' => $payload->get('residence_status'),
                'status' => 2,
            ]);

            if ($data->wasRecentlyCreated) {
                $data->created_at = Carbon::now()->timestamp;

                if (str_contains($req->url(), 'api')) {
                    $token = $req->bearerToken();
                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                        if (!$token) {
                            return Response()->json(['message' => 'Unauthorized'], 401);
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

    // #PUT
    public function edit(Update $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = FinancialAssistance::withTrashed()
                ->find(['id' => $payload->get('id')])
                ->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'request_by' => $payload->has('request_by') ? $payload->get('request_by') : $data->request_by,
                        'childrens' => $payload->has('childrens') ? $payload->get('childrens') : $data->childrens,
                        'salary' => $payload->has('salary') ? $payload->get('salary') : $data->salary,
                        'expenses' => $payload->has('expenses') ? $payload->get('expenses') : $data->expenses,
                        'job_status' => $payload->has('job_status') ? $payload->get('job_status') : $data->job_status,
                        'residence_status' => $payload->has('residence_status') ? $payload->get('residence_status') : $data->residence_status,
                        'status' => $payload->has('status') ? $payload->get('status') : $data->status,
                        'updated_by' => Auth::id(),
                    ]);
                } else {
                    $token = $req->bearerToken();
                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                        if (!$token) {
                            return Response()->json(['message' => 'Unauthorized'], 401);
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->update([
                        'request_by' => $payload->has('request_by') ? $payload->get('request_by') : $data->request_by,
                        'childrens' => $payload->has('childrens') ? $payload->get('childrens') : $data->childrens,
                        'salary' => $payload->has('salary') ? $payload->get('salary') : $data->salary,
                        'expenses' => $payload->has('expenses') ? $payload->get('expenses') : $data->expenses,
                        'job_status' => $payload->has('job_status') ? $payload->get('job_status') : $data->job_status,
                        'residence_status' => $payload->has('residence_status') ? $payload->get('residence_status') : $data->residence_status,
                        'status' => $payload->has('status') ? $payload->get('status') : $data->status,
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

    // #DELETE
    public function destroy(Delete $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = FinancialAssistance::withTrashed()
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
                            return Response()->json(['message' => 'Unauthorized'], 401);
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

    public function createView()
    {
        $users = User::all();

        return view('test.bansosCreate', compact('users'));
    }

    public function store(Request $request)
    {
        $data = FinancialAssistance::firstOrCreate([
            'request_by' => $request->request_by,
            'tanggungan' => $request->tanggungan,
            'alasan' => $request->alasan,
            'status' => $request->statusRadio,
        ]);

        return redirect('/bansos');
    }

    public function detail($bansos_id)
    {
        $data = FinancialAssistance::where('id', $bansos_id)->get()->first();

        return view('test.bansosDetail', compact('data'));
    }

    public function editView($bansos_id)
    {
        $users = User::all();
        $data = FinancialAssistance::where('id', $bansos_id)->get()->first();

        return view('test.bansosEdit', compact('users', 'data'));
    }

    public function update(Request $request, $bansos_id)
    {
        $data = FinancialAssistance::where('id', $bansos_id)->get()->first();

        $data->request_by = $request->request_by;
        $data->tanggungan = $request->tanggungan;
        $data->alasan = $request->alasan;
        $data->status = $request->statusRadio;

        $data->save();

        return redirect('/bansos');
    }

    public function delete($bansos_id)
    {
        $data = FinancialAssistance::where('id', $bansos_id)->delete();

        return redirect('/bansos');
    }
}
