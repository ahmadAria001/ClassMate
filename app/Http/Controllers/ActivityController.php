<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Docs\CreateActivity;
use App\Http\Requests\Resources\Docs\DeleteActivity;
use App\Http\Requests\Resources\Docs\UpdateActivity;
use App\Models\Activity;
use App\Models\Docs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;

class ActivityController extends Controller
{
    public function __invoke(){
        return Inertia::render();
    }

    public function get($filter = null)
    {
        $data = null;

        if ($filter)
            $data = Activity::with('docs_id','created_by','updated_by')->find($filter);
        else
            $data = Activity::with('docs_id','created_by','updated_by')->get();

        return Response()->json(['data' => $data], 200);
    }
    public function create(CreateActivity $request){
        $payload = $request->safe()->collect();

        try{
            $docs = Docs::create([
                'description' => $payload->get('description'),
                'type' => 'Event'
            ]);
            $data = Activity::create([
                'docs_id' => $docs->id,
                'name' => $payload->get('name'),
                'startDate' => strtotime($payload->get('startDate')),
                'endDate' => strtotime($payload->get('endDate')),
            ]);

            
            if ($data->wasRecentlyCreated) {
                $docs->created_at = Carbon::now()->timestamp;
                $data->created_at = Carbon::now()->timestamp;
                
                if (str_contains($request->url(), 'api')) {
                    $token = $request->bearerToken();
                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                        if (!$token) {
                            return Response()->json(['message'=>'Unauthorized'],401);
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $docs->created_by = ($model->get('id'))[0]->id;
                    $data->created_by = ($model->get('id'))[0]->id;
                } else{
                    $docs->created_by = Auth::id();
                    $data->created_by = Auth::id();
                }
                $docs->save();
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

        }catch(\Throwable $th){
            error_log($th);
        }
    }
    public function edit(UpdateActivity $request){
        $payload = $request->safe()->collect();

        try{
            $data = Activity::withTrashed()->where('id',$payload->get('id'))->first();
            $docs = Docs::withTrashed()->where('id',$data->docs_id)->first();
            error_log($data);
            if($data){
                //Handle Log Update
                if(Auth::guard('web')->check()){
                    $data->update([ 
                        'name' => ($payload->get('name')) ? $payload->get('name') : $data->endDate,
                        'startDate' => ($payload->get('startDate')) ? strtotime($payload->get('startDate')) : $data->startDate,
                        'endDate' => ($payload->get('endDate')) ? strtotime($payload->get('endDate')) : $data->endDate,
                        'updated_by' => Auth::id()
                    ]);

                    $docs->update([
                        'description' => ($payload->get('description')) ?  $payload->get('description') : $docs->description,
                        'updated_by' => Auth::id()
                    ]);
                }
                else{
                    $token = $request->bearerToken();
                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                        if (!$token) {
                            return Response()->json(['message'=>'Unauthorized'],401);
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->update([ 
                        'name' => ($payload->get('name')) ? $payload->get('name') : $data->name,
                        'startDate' => ($payload->get('startDate')) ? strtotime($payload->get('startDate')) : $data->startDate,
                        'endDate' => ($payload->get('endDate')) ? strtotime($payload->get('endDate')) : $data->endDate,
                        'updated_by' => ($model->get('id'))[0]->id
                    ]);

                    $docs->update([
                        'description' => ($payload->get('description')) ?  $payload->get('description') : $docs->description,
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
            
        }
        catch(\throwable $th){
             error_log($th);
        }
    }
    public function destroy(DeleteActivity $request){
        $payload = $request->safe()->collect();

        try{
            $data = Activity::withTrashed()->where('id',$payload->get('id'))->first();
            $docs = Docs::withTrashed()->where('id',$data->docs_id)->first();

            if($data){
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'deleted_by' => Auth::id()
                    ]);
                } else {
                    $token = $request->bearerToken();
                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                        if (!$token) {
                            return Response()->json(['message'=>'Unauthorized'],401);
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();
                    $data->update([
                        'deleted_by' => ($model->get('id'))[0]->id
                    ]);
                    $docs->update([
                        'deleted_by' => ($model->get('id'))[0]->id
                    ]);
                }
                $data->delete();
                $docs->delete();
                return Response()->json([
                    'status' => true,
                    'message' => 'Data Deleted'
                ]);
            }
            return Response()->json([
                'status' => false,
                'message' => 'Data Not found'
            ], 400);
        }catch(\Throwable $th){
            error_log($th);
        }
    }
}
