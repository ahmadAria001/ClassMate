<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resources\Docs\CreateDocumentation;
use App\Http\Requests\Resources\Docs\DeleteDocumentation;
use App\Http\Requests\Resources\Docs\UpdateDocumentation;
use App\Models\Docs;
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
    public function create(CreateDocumentation $request){
        $payload = $request->safe()->collect();

        try{
            $docs = Docs::create([
                'description' => $payload->get('description'),
                'type' => 'Administration'
            ]);

            $data = Documentation::create([
                
            ]);
        }
        
        catch(\Throwable $th){
            error_log($th);
        }
    }
    public function edit(UpdateDocumentation $request){

    }
    public function destroy(DeleteDocumentation $request){

    }
}
