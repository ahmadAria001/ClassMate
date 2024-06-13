<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Dues\CreateLog;
use App\Http\Requests\Resources\Dues\DeleteLog;
use App\Http\Requests\Resources\Dues\UpdateLog;
use App\Models\Civilian;
use App\Models\Dues;
use App\Models\DuesMember;
use App\Models\DuesPaymentLog;
use App\Models\User;
use App\Utils\AccessToken;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;

class DuesPaymentLogController extends Controller
{
    public function __invoke()
    {
        return Inertia::render();
    }

    public function get($filter = null)
    {
        $data = null;

        if ($filter)
            $data = DuesPaymentLog::where('id', $filter)
                ->skip(0)
                ->take(10)
                ->get();
        else
            $data = DuesPaymentLog::skip(0)
                ->take(10)
                ->get();

        return Response()->json(['data' => $data], 200);
    }

    public function getDuesTypes($rt)
    {
        $data = Dues::withoutTrashed()
            ->where('rt_id', '=', $rt)->get(['id', 'typeDues', 'amt_dues', 'status', 'created_at']);

        $length = $data->count();

        return Response()->json([
            'data' => $data->all(), 'length' => $length
        ], 200);
    }

    public function getMember($member, $dues, $page)
    {
        $take = 10;

        $isMember = DuesMember::withoutTrashed()->where('member', '=', $member)->where('dues', '=', $dues)->first();
        $data =
            DuesPaymentLog::withoutTrashed()
            ->with('dues_member.dues')
            ->with('dues_member.member')
            ->whereHas('dues_member.dues', function ($q) use ($dues) {
                return $q->where('id', $dues);
            })
            ->whereHas('dues_member.member', function ($q) use ($member) {
                return $q->where('id', $member);
            })
            ->orderByRaw('FROM_UNIXTIME(`paid_for`) DESC ')
            ->skip($page > 1 ? ($page - 1) * $take : 0)
            ->take($take)
            ->get();

        $length = $data->count();

        return Response()->json([
            'data' => $data->toArray(), 'length' => $length, 'isMember' => $isMember ? true : false, 'member' => $isMember,
        ], 200);
    }

    public function getDuesRT($rt_id): JsonResponse
    {
        $data = DuesPaymentLog::withoutTrashed()
            ->with('dues_member.dues')
            ->whereHas('dues_member.dues', function ($q) use ($rt_id) {
                $q->where('rt_id', '=', $rt_id);
            })
            ->get();

        $length = $data->count();

        return Response()->json(['data' => $data, 'lenght' => $length], 200);
    }

    public function getMonthlyIncomeLastSixMonths(Request $req)
    {
        $identity = AccessToken::getToken($req);

        if (!$identity) abort(401);
        $model = $identity->tokenable();
        $user = User::withoutTrashed()->with('civilian_id.rt_id')->where('id', $model->get('id')[0]->id)->get()->first();

        $isRT = $user->role == "RT";

        $currentDate = Carbon::now();
        $sixMonthsAgo = $currentDate->copy()->setMonth(12)->setDay(1)->setYear(2021)->subYear(1)->subMonths(12);

        // ngambil data pengeluaran dalam 6 bulan terakhir dan menjumlahkan perbulan
        $query =  $isRT ?
            'select
        DATE_FORMAT(FROM_UNIXTIME(`dpl`.created_at), "%Y-%m") as month,
        SUM(`dpl`.amount_paid) as total_amount
    from duesPaymentLog dpl
    JOIN dues_member dm
        ON dm.id = dpl.dues_member
    JOIN dues d
        ON d.id = dm.`member`
    where
        `dpl`.`deleted_at` is null
        and
        `dpl`.`created_at` >= UNIX_TIMESTAMP("' . $sixMonthsAgo->toDateString() . '")
        and
        d.rt_id = ' . $user->getRelation('civilian_id')->rt_id . '
    group by `month`
    order by `month` desc;'
            :
            'select
        DATE_FORMAT(FROM_UNIXTIME(`dpl`.created_at), "%Y-%m") as month,
        SUM(`dpl`.amount_paid) as total_amount
    from duesPaymentLog dpl
    JOIN dues_member dm
        ON dm.id = dpl.dues_member
    JOIN dues d
        ON d.id = dm.`member`
    where
        `dpl`.`deleted_at` is null
        and
        `dpl`.`created_at` >= UNIX_TIMESTAMP("' . $sixMonthsAgo->toDateString() . '")
    group by `month`
    order by `month` desc;';

        $monthlyIncome = DB::select($query);

        return response()->json([
            'data' => $monthlyIncome,
        ], 200);
    }

    public function create(CreateLog $req)
    {
        $payload = $req->safe()->collect();
        $payments = $payload->get('payments');
        // dd($payments);

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

            for ($i = 0; $i < count($payments); $i++) {
                $payments[$i] = [
                    'dues_member' => $payments[$i]['dues_member'],
                    'amount_paid' => $payments[$i]['amount_paid'],
                    'paid_for' => $payments[$i]['paid_for'],
                    'created_at' => Carbon::now()->timestamp,
                    'created_by' => ($model->get('id'))[0]->id
                ];
            }
        } else {
            for ($i = 0; $i < count($payments); $i++) {
                $payments[$i] = [
                    'dues_member' => $payments[$i]['dues_member'],
                    'amount_paid' => $payments[$i]['amount_paid'],
                    'paid_for' => $payments[$i]['paid_for'],
                    'created_at' => Carbon::now()->timestamp,
                    'created_by' => Auth::id()
                ];
            }
        }

        try {
            $member = DuesMember::withoutTrashed()->find($payments[0]['dues_member'])->get()->first();

            if (!$member) return Response()->json([
                'status' => false,
                'message' => 'Tidak dapat menemukan Iuran'
            ], 400);

            $data = DuesPaymentLog::insert($payments);

            if ($data) {
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

    public function edit(UpdateLog $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = DuesPaymentLog::withTrashed()->where('id', $payload->get('id'))->first();
            $oldAmt = $data->amount_paid;
            $dues = Dues::withTrashed()->where('id', $data->dues_id)->first();

            if ($data) {
                //Handle Log Update
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'amount_paid' => $payload->get('amount_paid'),
                        'exchange' => ($dues->amt_dues <= $payload->get('amount_paid')) ? $payload->get('amount_paid') - $dues->amt_dues  : 0,
                        'debt' => ($dues->amt_dues > $payload->get('amount_paid')) ? $dues->amt_dues - $payload->get('amount_paid') : 0,
                        'updated_by' => Auth::id()
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
                        'amount_paid' => $payload->get('amount_paid'),
                        'exchange' => ($dues->amt_dues <= $payload->get('amount_paid')) ? $payload->get('amount_paid') - $dues->amt_dues  : 0,
                        'debt' => ($dues->amt_dues > $payload->get('amount_paid')) ? $dues->amt_dues - $payload->get('amount_paid') : 0,
                        'updated_by' => ($model->get('id'))[0]->id
                    ]);
                }
                //Update Dues Fund
                //Subtract old amt
                if ($dues->amt_dues > $oldAmt) { //If debt
                    $dues->amt_fund -= $oldAmt;
                } else { //If not debt
                    $dues->amt_fund -= $dues->amt_dues;
                }

                //Add new amt
                if ($dues->amt_dues > $payload->get('amount_paid')) { //If Debt
                    $dues->amt_fund += $payload->get('amount_paid');
                } else { //If not debt
                    $dues->amt_fund += $dues->amt_dues;
                }

                $dues->save();

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

    public function destroy(DeleteLog $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = DuesPaymentLog::withTrashed()->where('id', $payload->get('id'))->first();
            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'deleted_by' => Auth::id()
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
                        'deleted_by' => ($model->get('id'))[0]->id
                    ]);
                }
                $oldAmt = $data->amount_paid;
                $dues = Dues::withTrashed()->find($data->dues_id);

                if ($dues->amt_dues > $oldAmt) { //If debt
                    $dues->amt_fund -= $oldAmt;
                } else { //If not debt
                    $dues->amt_fund -= $dues->amt_dues;
                }

                $dues->save();
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
