<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\User\Pict\Create;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Laravel\Sanctum\PersonalAccessToken;

class ProfileImageController extends Controller
{
    public function get($filter)
    {
        $model = User::withoutTrashed()->where('id', $filter)->first();
        return Response()->json(['pict' => $model->pict], 200);
    }

    public function create(Create $req)
    {
        $image = $req->file('img');

        $user = null;

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

            $user = $model->get('id')[0]->id;
        } else {
            $user = Auth::id();
        }

        $model = User::withoutTrashed()->where('id', $user)->first();

        // File::makeDirectory(public_path('assets/uploads/'), 0755, true, true);
        $name = Carbon::now() . '_' . $image->getClientOriginalName();
        $path = public_path('assets/uploads') . '/' . $name;
        // [$width, $height] = getimagesize($image->getFileInfo());

        Image::read($image)->resize(480, 480)->toJpeg()->save($path);

        $model->pict = $name;
        $model->save();

        return Response()->json(['status' => true, 'message' => 'Operation Success'], 200);
    }

    public function edit()
    {
        //
    }

    public function destroy()
    {
        //
    }
}
