<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Docs\CreateComplaint;
use App\Http\Requests\Resources\Docs\DeleteComplaint;
use App\Http\Requests\Resources\Docs\UpdateComplaint;
use App\Models\Complaint;
use App\Models\Docs;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Image\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;
use ReflectionClass;
use App\Utils\AccessToken;

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
        error_log(strlen($filter));

        if ($filter && strlen($filter) > 0) {
            $data = Complaint::withoutTrashed()->with('docs_id', 'created_by.civilian_id', 'updated_by')
                ->where('id', $filter)->first();
        } else {
            $data = Complaint::withoutTrashed()->with('docs_id', 'created_by.civilian_id', 'updated_by')->get();
        }

        $length = $data->count();

        return Response()->json(['data' => $data, 'length' => $length]);
    }

    public  function getLike($page = 1, $filter = null)
    {
        $data = null;
        $take = 5;

        if ($filter) {
            $data = Complaint::withoutTrashed()->with('docs_id', 'created_by.civilian_id', 'updated_by')
                ->whereHas('created_by.civilian_id', function ($q) use ($filter) {
                    $q->whereAny(['fullName'], 'LIKE', "%$filter%");
                    // $q->where('fullName', 'LIKE', "%$filter%");
                })
                ->skip($page > 1 ? ($page - 1) * $take : 0)
                ->take($take);

            // dd($data);
        } else {
            $data = Complaint::withoutTrashed()->with('docs_id', 'created_by.civilian_id', 'updated_by');
        }

        $length = $data->count();

        return Response()->json(['data' => $data->get(), 'length' => $length]);
    }

    public function getPaged($page = 1)
    {
        $take = 5;

        $data = Complaint::withoutTrashed()
            ->with('docs_id', 'created_by.civilian_id', 'updated_by')
            ->skip($page > 1 ? ($page - 1) * $take : 0)
            ->take($take)
            ->get();

        $length = Complaint::withoutTrashed()->count();

        return response()->json(['data' => $data, 'length' => $length]);
    }

    public function getByWarga(Request $req, $page = 1)
    {
        $data = null;
        $take = 5;
        $identity = AccessToken::getToken($req);

        if (!$req) abort(401);
        $model = $identity->tokenable();
        $user = User::withoutTrashed()->with('civilian_id.rt_id')->where('id', $model->get('id')[0]->id)->get()->first();

        $data = Complaint::withoutTrashed()
            ->with('docs_id', 'created_by.civilian_id', 'updated_by')
            ->whereHas('created_by', function ($q) use ($user) {
                $q->where(
                    'id',
                    $user->id
                );
            })
            ->skip($page > 1 ? ($page - 1) * $take : 0)
            ->take($take)
            ->get();

        $length = $data->count();

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

        $data = Complaint::withoutTrashed()
            ->with('docs_id', 'created_by.civilian_id', 'updated_by')
            ->whereHas('created_by.civilian_id.rt_id', function ($q) use ($user) {
                $q->where(
                    'id',
                    $user->getRelation('civilian_id')->rt_id
                );
            })
            ->skip($page > 1 ? ($page - 1) * $take : 0)
            ->take($take)
            ->get();

        $length = Complaint::withoutTrashed()->count();

        return response()->json(['data' => $data, 'length' => $length]);
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

                if (!Storage::directoryExists('public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads')) {
                    // File::makeDirectory(, 0755, true, true);
                    Storage::makeDirectory('public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads');
                }

                $path =
                    // public_path('storage') . DIRECTORY_SEPARATOR .
                    'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

                // [$width, $height] = getimagesize($image->getFileInfo());
                // $loadedImg = Image::load($image->getRealPath());
                // $compressedImg = $loadedImg
                //     ->width($width > 1080 ? 1080 : $width)
                //     ->height($height > 1080 ? 1080 : $height)
                //     ->quality(20)
                //     ->save(Storage::path($path) . $name);
                $image->storePubliclyAs($path . $name);
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

                    if (!Storage::directoryExists('public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads')) {
                        // File::makeDirectory(, 0755, true, true);
                        Storage::makeDirectory('public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads');
                    }

                    $path =
                        // public_path('storage') . DIRECTORY_SEPARATOR .
                        'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

                    // [$width, $height] = getimagesize($image->getFileInfo());
                    // $loadedImg = Image::load($image->getRealPath());
                    // $compressedImg = $loadedImg
                    //     ->width($width > 1080 ? 1080 : $width)
                    //     ->height($height > 1080 ? 1080 : $height)
                    //     ->quality(20)
                    //     ->save(Storage::path($path) . $name);
                    $image->storePubliclyAs($path . $name);

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
