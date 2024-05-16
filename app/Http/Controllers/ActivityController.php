<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __invoke(){

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
}
