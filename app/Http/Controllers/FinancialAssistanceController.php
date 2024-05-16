<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\FA\Create;
use App\Http\Requests\Resources\FA\Delete;
use App\Http\Requests\Resources\FA\Update;
use App\Models\FinancialAssistance;
use App\Models\RT;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class FinancialAssistanceController extends Controller
{
    public function __invoke()
    {
        $data = FinancialAssistance::all();

        return view('test.bansosList', ['data' => $data]);
    }

    // #GET
    public function get($filter = null)
    {
        $data = null;

        // if ($filter)
        //     $data = FinancialAssistance::with('civilian.family.rt_id')->find($filter);
        // else
        //     $data = FinancialAssistance::with('civilian.family.rt_id')->get();
        if ($filter)
            $data = RT::with(['family.civil.fa'])->find($filter);
        else
            $data = RT::with('family.civil.fa')->get();

        return Response()->json(['data' => $data], 200);
    }

    // #POST
    public function create(Create $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = FinancialAssistance::firstOrCreate([
                'request_by' => $payload->get('request_by'),
                'tanggungan' => $payload->get('tanggungan'),
                'alasan' => $payload->get('alasan'),
                'status' => $payload->get('status'),
            ]);

            if ($data->wasRecentlyCreated) {
                $data->created_at = Carbon::now()->timestamp;

                if (str_contains($req->url(), 'api')) {
                    $token = $req->bearerToken();
                    $pat = PersonalAccessToken::findToken($token);

                    $model = $pat->tokenable();

                    $data->created_by = ($model->get('id'))[0]->id;
                } else
                    $data->created_by = Auth::id();

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

    // #PUT
    public function edit(Update $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = FinancialAssistance::withTrashed()->find(['id' => $payload->get('id')])->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'request_by' => $payload->get('request_by'),
                        'tanggungan' => $payload->get('tanggungan'),
                        'alasan' => $payload->get('alasan'),
                        'status' => $payload->get('status'),
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    $token = $req->bearerToken();
                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->update([
                        'request_by' => $payload->get('request_by'),
                        'tanggungan' => $payload->get('tanggungan'),
                        'alasan' => $payload->get('alasan'),
                        'status' => $payload->get('status'),
                        'updated_by' => ($model->get('id'))[0]->id
                    ]);
                }

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

    // #DELETE
    public function destroy(Delete $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = FinancialAssistance::withTrashed()->find(['id' => $payload->get('id')])->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'deleted_by' => Auth::id()
                    ]);
                } else {
                    $token = $req->bearerToken();
                    $pat = PersonalAccessToken::findToken($token);

                    $model = $pat->tokenable();
                    $data->update([
                        'deleted_by' => ($model->get('id'))[0]->id
                    ]);
                }

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
            'status' => $request->statusRadio
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
