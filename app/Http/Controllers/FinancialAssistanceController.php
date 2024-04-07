<?php

namespace App\Http\Controllers;

use App\Models\FinancialAssistance;
use App\Models\User;
use Illuminate\Http\Request;

class FinancialAssistanceController extends Controller
{
    public function __invoke(){
        $data = FinancialAssistance::all();

        return view('test.bansosList',['data' => $data]);
    }
    public function create(){
        $users = User::all();

        return view('test.bansosCreate', compact('users'));
    }

    public function store(Request $request){

        $data = FinancialAssistance::firstOrCreate([
            'request_by' => $request->request_by,
            'tanggungan' => $request->tanggungan,
            'alasan' => $request->alasan,
            'status' => $request->statusRadio
        ]);

        return redirect('/bansos');
    }

    public function detail($bansos_id){
        $data = FinancialAssistance::where('id',$bansos_id)->get()->first();

        return view('test.bansosDetail', compact('data'));
    }

    public function edit($bansos_id){
        $users = User::all();
        $data = FinancialAssistance::where('id',$bansos_id)->get()->first();

        return view('test.bansosEdit', compact('users','data'));
    }

    public function update(Request $request, $bansos_id){
        $data = FinancialAssistance::where('id',$bansos_id)->get()->first();

        $data->request_by = $request->request_by;
        $data->tanggungan = $request->tanggungan;
        $data->alasan = $request->alasan;
        $data->status = $request->statusRadio;

        $data->save();

        return redirect('/bansos');
    }

    public function delete($bansos_id){
        $data = FinancialAssistance::where('id',$bansos_id)->delete();

        return redirect('/bansos');
    }
}
