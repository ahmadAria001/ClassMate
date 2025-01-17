<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Family\Create;
use App\Http\Requests\Resources\Family\Delete;
use App\Http\Requests\Resources\Family\Update;
use App\Models\Civilian;
use App\Models\Family;
use App\Models\RT;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;

class FamilyController extends Controller
{
    public function __invoke()
    {
        $data = Family::All();
        return view('test.familyList', ['data' => $data]);
    }

    // #GET
    public function get($filter = null)
    {
        $data = null;

        if ($filter)
            $data = Family::with('rt_id')->with('civil')->find($filter);
        else
            $data = Family::with('rt_id')->with('civil')->get();

        return Response()->json(['data' => $data], 200);
    }

    // #POST
    public function create(Create $req)
    {
        $payload = $req->safe()->collect();

        try {
            $exist = Family::find([
                'nkk' => $payload->get('nkk'),
            ]);

            if (count($exist) > 0) {
                return Response()->json([
                    'status' => false,
                    'message' => 'Data already exist'
                ], 400);
            }

            $data = Family::firstOrCreate([
                'nkk' => $payload->get('nkk'),
                'residentstatus' => $payload->get('residentstatus') == 'PermanentResident' ? 'PermanentResident' : 'ContractResident',
                'rt_id' => $payload->get('rt_id'),
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
            $data = Family::withTrashed()->find(['id' => $payload->get('id')])->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'nkk' => $payload->get('nkk'),
                        'residentstatus' => $payload->get('residentstatus') == 'PermanentResident' ? 'PermanentResident' : 'ContractResident',
                        'rt_id' => $payload->get('rt_id'),
                        'updated_by' => Auth::id()
                    ]);
                } else {
                    $token = $req->bearerToken();
                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->update([
                        'nkk' => $payload->get('nkk'),
                        'residentstatus' => $payload->get('residentstatus') == 'PermanentResident' ? 'PermanentResident' : 'ContractResident',
                        'rt_id' => $payload->get('rt_id'),
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
            $data = Family::withTrashed()->find(['id' => $payload->get('id')])->first();

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

    public function viewCreate()
    {
        $rt = RT::all();

        return view('test.familyCreate', compact('rt'));
    }

    public function store(Request $request)
    {
        $data = Family::firstOrCreate([
            'nkk' => $request->nkk,
            'rt_id' => $request->rt_id,
            'residentstatus' => ($request->ResidencyRadio == 'PermanentResident') ? 'PermanentResident' : 'ContractResident',
            'created_at' => Carbon::now()->timestamp
        ]);

        return redirect('/family');
    }

    public function detail($family_id)
    {
        $header_data = Family::where('id', $family_id)->get()->first();
        $data = Civilian::where('family_id', $family_id)->get();

        return view('test.familyDetail', compact('header_data', 'data'));
    }

    public function editView($family_id)
    {
        $data = Family::where('id', $family_id)->get()->first();
        $rt = RT::all();

        return view('/test.familyEdit', compact('data', 'rt'));
    }

    public function update(Request $request, $family_id)
    {
        $data = Family::where('id', $family_id)->first();

        $data->nkk = $request->nkk;
        $data->rt_id = $request->rt_id;
        $data->residentstatus = ($request->ResidencyRadio == 'PermanentResident') ? 'PermanentResident' : 'ContractResident';

        $data->save();

        return redirect('/family');
    }

    public function delete($family_id)
    {
        $data = Family::where('id', $family_id)->delete();

        return redirect('/family');
    }
}
