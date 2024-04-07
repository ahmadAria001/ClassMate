<?php

namespace App\Http\Controllers;

use App\Models\Civilian;
use App\Models\Family;
use App\Models\RT;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    public function __invoke(){
        $data = Family::All();
        return view('test.familyList' ,['data' => $data]);
    }

    public function create(){
        $rt = RT::all();

        return view('test.familyCreate', compact('rt'));
    }

    public function store(Request $request){
        $data = Family::firstOrCreate([
            'nkk' => $request->nkk,
            'rt_id' => $request->rt_id,
            'residentstatus' => ($request->ResidencyRadio == 'PermanentResident') ? 'PermanentResident' : 'ContractResident',
            'created_at' => Carbon::now()->timestamp
        ]);

        return redirect('/family');
    }

    public function detail($family_id){
        $header_data = Family::where('id', $family_id)->get()->first();
        $data = Civilian::where('family_id', $family_id)->get();

        return view('test.familyDetail',compact('header_data','data'));
    }

    public function edit($family_id){
        $data = Family::where('id',$family_id)->get()->first();
        $rt = RT::all();

        return view('/test.familyEdit',compact('data','rt'));
    }

    public function update(Request $request, $family_id){
        $data = Family::where('id',$family_id)->first();

        $data->nkk = $request->nkk;
        $data->rt_id = $request->rt_id;
        $data->residentstatus = ($request->ResidencyRadio == 'PermanentResident') ? 'PermanentResident' : 'ContractResident';

        $data->save();
        
        return redirect('/family');
    }

    public function delete($family_id){
        $data = Family::where('id',$family_id)->delete();

        return redirect('/family');
    }

}
