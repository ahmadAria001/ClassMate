<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\User\Pict\Create;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

class ProfileImageController extends Controller
{
    public function get($filter)
    {
        $model = User::withoutTrashed()->where('id', '=', $filter)->first();
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

        $name = Carbon::now()->timestamp . '_' . $image->hashName();

        try {
            if (!Storage::directoryExists('public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads')) {
                // File::makeDirectory(, 0755, true, true);
                Storage::makeDirectory('public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads');
            }

            $path =
                // public_path('storage') . DIRECTORY_SEPARATOR .
                'public' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

            // $loadedImg = Image::load($image->getRealPath());
            // $compressedImg = $loadedImg
            //     ->width(480)
            //     ->height(480)
            //     ->quality(20)
            //     ->save(Storage::path($path) . $name);

            // Image::read($image)->resizeDown(480, 480)->toJpeg();
            // Storage::putFileAs($path, $compressedImg, $name);
            // [$width, $height] = getimagesize($image->getFileInfo());

            // error_log($name);
            // error_log($compressedImg);

            $storedImg = $image->storePubliclyAs($path . $name);

            error_log($storedImg);

            $model->pict = $name;
            $model->save();

            return Response()->json(['status' => true, 'message' => 'Operation Success'], 200);
        } catch (\Throwable $th) {
            error_log('Line ' . $th->getLine() . ' ' . $th->getFile());
            error_log($th->getMessage());
            return Response()->json(['status' => false, 'message' => 'Something went wrong'], 500);
        }
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
