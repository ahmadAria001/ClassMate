<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Docs\CreateDocumentation;
use App\Http\Requests\Resources\Docs\DeleteDocumentation;
use App\Http\Requests\Resources\Docs\UpdateDocumentation;
use App\Models\Docs;
use App\Models\Documentation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;

class DocumentationController extends Controller
{
    public function __invoke()
    {
        return Inertia::render();
    }

    public function get($filter = null)
    {
        $data = null;

        if ($filter) {
            $data = Documentation::withoutTrashed()->with('docs_id', 'created_by', 'updated_by')->find($filter);
        } else {
            $data = Documentation::withoutTrashed()->with('docs_id', 'created_by', 'updated_by')->get();
        }

        return Response()->json(['data' => $data], 200);
    }
    public function create(CreateDocumentation $request){
        $payload = $request->safe()->collect();

        try{
            $docs = Docs::create([
                'description' => $payload->get('description'),
                'type' => 'Administration'
            ]);

            
            $image = $request->file('contentAttachment');
            $name = ($image) ? Carbon::now() . '_' . $image->getClientOriginalName() : null;
            
            $data = Documentation::create([
                'docs_id'=>$docs->id,
                'contentType' => $payload->get('contentType'),
                'contentDesc' => $payload->get('contentDesc'),
                'contentAttachment' => ($image) ? $name : null
            ]);
            /* Image Upload
            if($image){
                $path = public_path('assets.uploads'). $name; // Unix Backslash :PepeHands
                Image::read($image)->save($path);
            }
            */
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
    public function edit(UpdateDocumentation $request){
        $payload = $request->safe()->collect();

        try{
            $data = Documentation::withTrashed()->where('id',$payload->get('id'))->first();
            $docs = Docs::withTrashed()->where('id',$data->docs_id)->first();
            
            if($data){
                $image = $request->file('contentAttachment');
                $name = ($image) ? Carbon::now() . '_' . $image->getClientOriginalName() : null;
            
                //Handle Log Update
                if(Auth::guard('web')->check()){
                    $data->update([
                        'contentType' => ($payload->get('contentType')) ? $payload->get('contentType') : $data->contentType,
                        'contentDesc' => ($payload->get('contentDesc')) ? $payload->get('contentDesc') : $data->contentDesc,
                        'contentAttachment' => ($image) ? $name : $data->contentAttachment,
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
                        'contentType' => ($payload->get('contentType')) ? $payload->get('contentType') : $data->contentType,
                        'contentDesc' => ($payload->get('contentDesc')) ? $payload->get('contentDesc') : $data->contentDesc,
                        'contentAttachment' => ($image) ? $name : $data->contentAttachment,
                        'updated_by' => ($model->get('id'))[0]->id
                    ]);

                    $docs->update([
                        'description' => ($payload->get('description')) ?  $payload->get('description') : $docs->description,
                        'updated_by' => ($model->get('id'))[0]->id
                    ]);
                }

                /* Image Upload
                if($image){
                    $path = public_path('assets.uploads'). $name; // Unix Backslash :PepeHands
                    Image::read($image)->save($path);
                }
                */

                return Response()->json([
                    'status' => true,
                    'message' => 'Data Updated'
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
    public function destroy(DeleteDocumentation $request){
        $payload = $request->safe()->collect();

        try{
            $data = Documentation::withTrashed()->where('id',$payload->get('id'))->first();
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
