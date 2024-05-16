<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Docs\CreateComplaint;
use App\Http\Requests\Resources\Docs\DeleteComplaint;
use App\Http\Requests\Resources\Docs\UpdateComplaint;
use App\Models\Complaint;
use App\Models\Docs;
use Carbon\Carbon;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class ComplaintController extends Controller
{
    public function __invoke()
    {
    }

    public function get($filter = null)
    {
        $data = null;

        if ($filter) {
            $data = Complaint::withoutTrashed()->with('docs_id', 'created_by', 'updated_by')->find($filter);
        } else {
            $data = Complaint::withoutTrashed()->with('docs_id', 'created_by', 'updated_by')->get();
        }

        return Response()->json(['data' => $data], 200);
    }
    public function create(CreateComplaint $request){
        $payload = $request->safe()->collect();

        try{
            $docs = Docs::create([
                'description' => $payload->get('description'),
                'type' => 'Complaint'
            ]);

            $image = $request->file('attachment');
            $name = ($image) ? Carbon::now() . '_' . $image->getClientOriginalName() : null;
            
            $data = Complaint::create([
                'docs_id'=>$docs->id,
                'attachment' => ($image) ? $name : null,
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
    public function edit(UpdateComplaint $request){
        $payload = $request->safe()->collect();

        try{
            $data = Complaint::withTrashed()->where('id',$payload->get('id'))->first();
            $docs = Docs::withTrashed()->where('id',$data->docs_id)->first();
            error_log($payload);
            error_log($data);
            
            if($data){
                $image = $request->file('attachment');
                $name = ($image) ? Carbon::now() . '_' . $image->getClientOriginalName() : null;
            
                //Handle Log Update
                if(Auth::guard('web')->check()){
                    $data->update([
                        'complaintStatus'=> ($payload->get('complaintStatus')) ? $payload->get('complaintStatus') : $data->complaintStatus,
                        'attachment' => ($image) ? $name : $data->attachment,
                        'updated_by' => Auth::id()
                    ]);

                    $docs->update([
                        'description' => ($payload->get('description')) ?  $payload->get('description') : $docs->description,
                        'updated_by' => Auth::id()
                    ]);
                }
                else{
                    $token = $request->bearerToken();
                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->update([
                        'complaintStatus'=> ($payload->get('complaintStatus')) ? $payload->get('complaintStatus') : $data->complaintStatus,
                        'attachment' => ($image) ? $name : $data->attachment,
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
    public function destroy(DeleteComplaint $request){
        $payload = $request->safe()->collect();

        try{
            $data = Complaint::withTrashed()->where('id',$payload->get('id'))->first();
            $docs = Docs::withTrashed()->where('id',$data->docs_id)->first();

            if($data){
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'deleted_by' => Auth::id()
                    ]);
                } else {
                    $token = $request->bearerToken();
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
