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
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;
use ReflectionClass;

class ComplaintController extends Controller
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

        if ($pat->cant((new ReflectionClass($this))->getShortName() . ':__invoke')) {
            return abort(404);
        }

        return Inertia::render('StatusPengaduan');
    }

    public function manageComplaintView(Request $request)
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
        // dd($pat);

        if ($pat->cant((new ReflectionClass($this))->getShortName() . ':create') && $pat->cant((new ReflectionClass($this))->getShortName() . ':edit') && $pat->cant((new ReflectionClass($this))->getShortName() . ':destroy')) {
            return abort(404);
        }

        return Inertia::render('DaftarPengaduan');
    }

    public function restricted(Request $request)
    {
        if (str_contains($request->url(), 'api')) {
            $token = $request->bearerToken();
            if (!$token) {
                $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                if (!$token) {
                    return redirect('beranda');
                }
            }

            if ($token) {
                return redirect('beranda');
            }
        } else {
            $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

            if ($token) {
                return redirect('beranda');
            }
        }

        return Inertia::render('StatusPengaduan');
    }

    public function get($filter = null)
    {
        $data = null;

        if ($filter) {
            $data = Complaint::withoutTrashed()->with('docs_id', 'created_by.civilian_id', 'updated_by')->find($filter);
        } else {
            $data = Complaint::withoutTrashed()->with('docs_id', 'created_by.civilian_id', 'updated_by')->get();
        }

        return Response()->json(['data' => $data], 200);
    }
    public function create(CreateComplaint $request)
    {
        $payload = $request->safe()->collect();

        try {
            $data = null;

            $docs = Docs::firstOrCreate([
                'description' => $payload->get('description'),
                'type' => 'Complaint',
            ]);

            if ($request->has('attachment')) {
                $image = $request->file('attachment');
                // NOTE: thid only works on Unix(Linux) with chown or chmod command
                $name = Carbon::now() . '_' . $image->getClientOriginalName();

                $data = Complaint::firstOrCreate([
                    'docs_id' => $docs->id,
                    'attachment' => $image ? $name : null,
                ]);

                $path = public_path('assets/uploads') . '/' . $name;
                [$width, $height] = getimagesize($image->getFileInfo());

                Image::read($image)
                    ->resize($width > 1080 ? 1080 : $width, $height > 1080 ? 1080 : $height)
                    ->toJpeg()
                    ->save($path);
            } else {
                $data = Complaint::firstOrCreate([
                    'docs_id' => $docs->id,
                ]);
            }

            // FIX: Somehow Fix this for Broken Windows
            // NOTE: Possible Solution using "url" function
            // like `url(public_path('assets.uploads'). $name);`
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
                            return Response()->json(['message' => 'Unauthorized'], 401);
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $docs->created_by = $model->get('id')[0]->id;
                    $data->created_by = $model->get('id')[0]->id;
                } else {
                    $docs->created_by = Auth::id();
                    $data->created_by = Auth::id();
                }
                $docs->save();
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
    public function edit(UpdateComplaint $request)
    {
        $payload = $request->safe()->collect();

        try {
            $data = Complaint::withTrashed()->where('id', $payload->get('id'))->first();
            $docs = Docs::withTrashed()
                ->where('id', $data->docs_id)
                ->first();
            error_log($payload);
            error_log($data);

            if ($data) {
                //Handle Log Update
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'complaintStatus' => $payload->get('complaintStatus') ? $payload->get('complaintStatus') : $data->complaintStatus,
                        'updated_by' => Auth::id(),
                    ]);

                    $docs->update([
                        'description' => $payload->get('description'),
                        'updated_by' => Auth::id(),
                    ]);
                } else {
                    $token = $request->bearerToken();
                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
                        if (!$token) {
                            return Response()->json(['message' => 'Unauthorized'], 401);
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->update([
                        'complaintStatus' => $payload->get('complaintStatus') ? $payload->get('complaintStatus') : $data->complaintStatus,
                        'updated_by' => $model->get('id')[0]->id,
                    ]);

                    $docs->update([
                        'description' => $payload->get('description'),
                        'updated_by' => $model->get('id')[0]->id,
                    ]);
                }

                $image = null;
                if ($request->has('attachment')) {
                    $image = $request->file('attachment');
                }

                if (!$data->attachment || $image->getClientOriginalName() != $data->attachment) {
                    $name = $image ? Carbon::now() . '_' . $image->getClientOriginalName() : null;
                    $path = public_path('assets/uploads') . '/' . $name;
                    [$width, $height] = getimagesize($image->getFileInfo());

                    Image::read($image)
                        ->resize($width > 1080 ? 1080 : $width, $height > 1080 ? 1080 : $height)
                        ->toJpeg()
                        ->save($path);

                    $data->update(['attachment' => $name]);
                    $data->save();
                }

                /* Image Upload
                if($image){
                    $path = public_path('assets.uploads'). $name; // Unix Backslash :PepeHands
                    Image::read($image)->save($path);
                }
                */

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
    public function destroy(DeleteComplaint $request)
    {
        $payload = $request->safe()->collect();

        try {
            $data = Complaint::withTrashed()->where('id', $payload->get('id'))->first();
            $docs = Docs::withTrashed()
                ->where('id', $data->docs_id)
                ->first();

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'deleted_by' => Auth::id(),
                    ]);
                } else {
                    $token = $request->bearerToken();
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
                    $docs->update([
                        'deleted_by' => $model->get('id')[0]->id,
                    ]);
                }
                $data->delete();
                $docs->delete();
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
