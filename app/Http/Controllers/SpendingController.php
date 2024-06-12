<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Spendings\Create;
use App\Http\Requests\Resources\Spendings\Delete;
use App\Http\Requests\Resources\Spendings\Update;
use App\Models\Spending;
use App\Models\User;
use App\Utils\AccessToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;
use ReflectionClass;

class SpendingController extends Controller
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

        if ($pat->cant((new ReflectionClass($this))->getShortName() . ':create') && $pat->cant((new ReflectionClass($this))->getShortName() . ':edit') && $pat->cant((new ReflectionClass($this))->getShortName() . ':destroy')) {
            return abort(404);
        }

        return Inertia::render('Spendings');
    }

    // #GET
    public function get($filter = null)
    {
        $data = null;

        if ($filter) {
            $data = Spending::with(['created_by.civilian_id'])->find($filter);
        } else {
            $data = Spending::with('created_by.civilian_id')->orderByDesc('created_at')->get();
        }

        return Response()->json(['data' => $data], 200);
    }

    public function getMonthlyIncomeLastSixMonths()
    {
        $currentDate = Carbon::now();

        $sixMonthsAgo = $currentDate->copy()->subMonths(6);


        // dd($currentDate->toDate());
        // ngambil data pengeluaran dalam 6 bulan terakhir dan menjumlahkan perbulan

        $query =  'select
        DATE_FORMAT(FROM_UNIXTIME(created_at), "%Y-%m") as month,
        SUM(amount) as total_amount
    from `spendings`
    where
        `spendings`.`deleted_at` is null
        and
        `created_at` >= UNIX_TIMESTAMP("' . $sixMonthsAgo->toDateString() . '")
    group by `month`
    order by `month` desc; ';

        $monthlyIncome = DB::select($query);

        // $q =
        //     Spending::withoutTrashed()
        //     ->where('created_at', '>=', $sixMonthsAgo)
        //     ->where('created_at', '<=', Carbon::now())
        //     ->selectRaw('DATE_FORMAT(FROM_UNIXTIME(created_at), "%Y-%m") as month, SUM(amount) as total_amount')
        //     ->groupBy('month')
        //     ->orderBy('month', 'asc')
        //     ->toSql();

        return response()->json([
            'data' => $monthlyIncome,
        ], 200);
    }

    public function getPaged($page = 1)
    {
        $take = 5;

        $data = Spending::withoutTrashed()
            ->with('created_by.civilian_id.rt_id', 'updated_by.civilian_id.rt_id')
            ->orderByDesc('created_at')
            ->skip($page > 1 ? ($page - 1) * $take : 0)
            ->take($take)
            ->get();

        $length = Spending::withoutTrashed()->count();

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

        $data = Spending::withoutTrashed()
            ->with('created_by.civilian_id.rt_id', 'updated_by.civilian_id.rt_id')
            ->whereHas('created_by.civilian_id', function ($q) use ($user) {
                $q->where(
                    'id',
                    $user->getRelation('civilian_id')->rt_id
                );
            })
            ->orderByDesc('created_at')
            ->skip($page > 1 ? ($page - 1) * $take : 0)
            ->take($take)
            ->get();

        $length = Spending::withoutTrashed()->count();

        return response()->json(['data' => $data, 'length' => $length]);
    }

    // #POST
    public function create(Create $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = Spending::firstOrCreate([
                'category' => $payload->get('category'),
                'desc' => $payload->get('desc'),
                'amount' => $payload->get('amount'),
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
            $data = Spending::withTrashed()
                ->find(['id' => $payload->get('id')])
                ->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'category' => $payload->has('category') ? $payload->get('category') : $data->category,
                        'desc' => $payload->has('desc') ? $payload->get('desc') : $data->desc,
                        'amount' => $payload->has('amount') ? $payload->get('amount') : $data->amount,
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
                        'category' => $payload->has('category') ? $payload->get('category') : $data->category,
                        'desc' => $payload->has('desc') ? $payload->get('desc') : $data->desc,
                        'amount' => $payload->has('amount') ? $payload->get('amount') : $data->amount,
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
            $data = Spending::withTrashed()
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
}
