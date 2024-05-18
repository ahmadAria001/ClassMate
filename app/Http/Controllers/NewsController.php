<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Environment\Create;
use App\Http\Requests\Resources\Environment\Delete;
use App\Http\Requests\Resources\Environment\Update;
use App\Models\News;
use Carbon\Carbon;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;

class NewsController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Auth/RT');
    }

    public function get($filter = null)
    {
        $data = null;

        if ($filter) {
            $data = News::where('id', $filter)->get()->first();
        } else {
            $data = News::skip(0)->take(10)->get();
        }

        return Response()->json(['data' => $data], 200);
    }

    public function getlts()
    {
        $data = News::orderByDesc('created_at')->get();
        return Response()->json($data, 200);
    }

    public function create(Create $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = null;

            if ($payload->has('attachment')) {
                $image = $req->file('attachment');

                $name = Carbon::now() . '_' . $image->getClientOriginalName();
                $path = public_path('assets/uploads') . '/' . $name;
                // [$width, $height] = getimagesize($image->getFileInfo());

                Image::read($image)->resize(480, 480)->toJpeg()->save($path);

                $data = news::create([
                    'title' => $payload->get('title'),
                    'desc' => $payload->get('desc'),
                    'attachment' => $name,
                ]);
            } else {
                $data = news::create([
                    'title' => $payload->get('title'),
                    'desc' => $payload->get('desc'),
                ]);
            }

            if ($data->wasRecentlyCreated) {
                $data->created_at = Carbon::now()->timestamp;
                error_log($req->has('attachment'));
                if ($req->has('attachment')) {
                    $image = $req->file('attachment');

                    $name = Carbon::now() . '_' . $image->getClientOriginalName();
                    $path = public_path('assets/uploads') . '/' . $name;
                    // [$width, $height] = getimagesize($image->getFileInfo());

                    Image::read($image)->resize(480, 480)->toJpeg()->save($path); 

                    $data->attachment = $name;
                    $data->save();
                    error_log($data->attachment);
                }
                if (str_contains($req->url(), 'api')) {
                    $token = $req->bearerToken();

                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

                        if (!$token) {
                            return Response()->json(
                                [
                                    'message' => 'Unauthorized',
                                ],
                                401,
                            );
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);

                    $model = $pat->tokenable();

                    $data->created_by = $model->get('id')[0]->id;
                } else {
                    $data->created_by = Auth::id();
                }

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

    public function edit(Update $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = News::withoutTrashed()
                ->find(['id' => $payload->get('id')])
                ->first();

            $image = $req->file('attachment');

            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'title' => $payload->get('title'),
                        'desc' => $payload->get('desc'),
                        'updated_by' => Auth::id(),
                    ]);
                } else {
                    $token = $req->bearerToken();

                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

                        if (!$token) {
                            return Response()->json(
                                [
                                    'message' => 'Unauthorized',
                                ],
                                401,
                            );
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);
                    $model = $pat->tokenable();

                    $data->update([
                        'title' => $payload->get('title'),
                        'desc' => $payload->get('desc'),
                        'updated_by' => $model->get('id')[0]->id,
                    ]);
                }

                $name = Carbon::now() . '_' . $image->getClientOriginalName();
                $path = public_path('assets/uploads') . '/' . $name;
                [$width, $height] = getimagesize($image->getFileInfo());

                if (!$data->attachment || $image->getClientOriginalName() != $data->attachment) {
                    Image::read($image)
                        ->resize($width > 1080 ? 1080 : $width, $height > 1080 ? 1080 : $height)
                        ->toJpeg()
                        ->save($path);
                }

                $data->update(['attachment' => $name]);
                $data->save();

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

    public function destroy(Delete $req)
    {
        $payload = $req->safe()->collect();

        try {
            $data = News::withTrashed()
                ->find(['id' => $payload->get('id')])
                ->first();
            if ($data) {
                if (Auth::guard('web')->check()) {
                    $data->update([
                        'deleted_by' => Auth::id(),
                    ]);
                } else {
                    $token = $req->bearerToken();

                    if (!$token) {
                        $token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;

                        if (!$token) {
                            return Response()->json(
                                [
                                    'message' => 'Unauthorized',
                                ],
                                401,
                            );
                        }
                    }

                    $pat = PersonalAccessToken::findToken($token);

                    $model = $pat->tokenable();
                    $data->update([
                        'deleted_by' => $model->get('id')[0]->id,
                    ]);
                }

                $data->save();

                $data->delete();

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
