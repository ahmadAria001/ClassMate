<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

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
}
