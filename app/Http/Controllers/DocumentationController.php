<?php

namespace App\Http\Controllers;

use App\Models\Documentation;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function __invoke()
    {
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
}
