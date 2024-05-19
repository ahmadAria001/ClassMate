<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Docs\CreateRequestDoc;
use App\Http\Requests\Resources\Docs\DeleteRequestDoc;
use App\Http\Requests\Resources\Docs\UpdateRequestDoc;
use App\Models\DocRequest;
use App\Models\Docs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;

class DocRequestController extends Controller
{
    public function __invoke(){
        return Inertia::render('');
    }
    public function get($filter=null){
        $data = null;
        if($filter){
            $data = DocRequest::withoutTrashed()->with('created_by','updated_by')->find($filter);
        }
        else{
            $data = DocRequest::withoutTrashed()->with('created_by','updated_by')->get();
        }

        return response()->json(['data'=>$data], 200);
    }
    public function create(CreateRequestDoc $request){
            $payload = $request->safe()->collect();

            try{
                $docs = Docs::create([
                    'description' => $payload->get('description'),
                    'type' => 'Request'
                ]);

                $data = DocRequest::create([
                    'docs_id' => $docs->id,
                    'request_by'=>$payload->get('request_by')
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
            }
            catch(\Throwable $th){
                error_log($th);
            }
    }
    public function edit(UpdateRequestDoc $request){
        $payload = $request->safe()->collect();

        try{
            $data = DocRequest::withTrashed()->where('id',$payload->get('id'))->first();
            $docs = Docs::withTrashed()->where('id',$data->docs_id)->first();

            if($data){
                //Handle Log Update
                if(Auth::guard('web')->check()){
                    $data->update([
                        'requestStatus' => ($payload->get('requestStatus')) ? $payload->get('requestStatus') : $data->requestStatus,
                        'request_by'=> ($payload->get('request_by')) ?  $payload->get('request_by') : $data->request_by,
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
                        'requestStatus' => ($payload->get('requestStatus')) ? $payload->get('requestStatus') : $data->requestStatus,
                        'request_by'=> ($payload->get('request_by')) ?  $payload->get('request_by') : $data->request_by,
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
        catch(\Throwable $th){
            error_log($th);
        }
    }
    public function destroy(DeleteRequestDoc $request){
        $payload = $request->safe()->collect();

        try{
            $data = DocRequest::withTrashed()->where('id',$payload->get('id'))->first();
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
