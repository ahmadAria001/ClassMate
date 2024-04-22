<?php

namespace App\Http\Controllers;

use App\Models\Docs;
use Illuminate\Http\Request;

class DocsController extends Controller
{
    //
    public function __invoke(){

    }

    public function get($filter = null)
    {
        $data = null;

        if ($filter)
            $data = Docs::with('created_by','updated_by')->find($filter);
        else
            $data = Docs::with('created_by','updated_by')->get();

        return Response()->json(['data' => $data], 200);
    }

    
}
