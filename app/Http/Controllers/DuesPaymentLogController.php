<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Dues\CreateLog;
use App\Http\Requests\Resources\Dues\DeleteLog;
use App\Http\Requests\Resources\Dues\UpdateLog;
use App\Models\Dues;
use App\Models\DuesMember;
use App\Models\DuesPaymentLog;
use Carbon\Carbon;
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
            ->where('rt_id', '=', $rt)->get(['id', 'typeDues', 'amt_dues', 'status']);

        $length = $data->count();

        return Response()->json([
            'data' => $data->all(), 'length' => $length
        ], 200);
    }

    public function getMember($member, $dues, $page)
    {
        $take = 10;

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
            'data' => $data->toArray(), 'length' => $length
        ], 200);
    }

    public function create(CreateLog $req)
    {
        $payload = $req->safe()->collect();

        try {
            $compare = Dues::find($payload->get('dues_id'));

            $data = DuesPaymentLog::Create([
                'dues_id' => $payload->get('dues_id'),
                'paid_by' => $payload->get('paid_by'),
                'amount_paid' => $payload->get('amount_paid'),
                'exchange' => ($compare->amt_dues <= $payload->get('amount_paid')) ? $payload->get('amount_paid') - $compare->amt_dues : 0,
                'debt' => ($compare->amt_dues > $payload->get('amount_paid')) ? $compare->amt_dues - $payload->get('amount_paid') : 0
            ]);

            $dues = Dues::withTrashed()->where('id', $payload->get('dues_id'))->first();
            if ($dues->amt_dues > $payload->get('amount_paid')) { //If Debt
                $dues->amt_fund += $payload->get('amount_paid');
            } else { //If not debt
                $dues->amt_fund += $dues->amt_dues;
            }
            $dues->save();


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

                    $data->created_by = ($model->get('id'))[0]->id;
                } else {
                    $data->created_by = Auth::id();
                }
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
