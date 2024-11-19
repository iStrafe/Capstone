<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class adoptionController extends Controller
{
    public function showAdoptionForm(){
        return view('Services.adoptionForm');
    }

    public function showAdoptionRequest(){
        $adoption_request = DB::table('adoption_request')->get();
        return view('admin.adoptionRequest', ['adoption_request' => $adoption_request]);
    }


    public function showApprovedReqest(){
        $approved_requests = DB::table('adoption_request')->where('status', 'Approved')->get();
        return view('admin.adoptionRequest', ['approved_requests' => $approved_requests]);
    }

    public function showMyRequests() {
    $adoption_request = DB::table('adoption_request')->get();
    return view('myRequest', ['adoption_request' => $adoption_request]);
}

    public function create(Request $request){
        
        $data = [
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'home_phone' => $request->input('phone'),
            'mobile_phone' => $request->input('phone'),
            'valid_id' => $request->input('valid_id'),
            'name_of_cat' => $request->input('name_of_cat'),
            'breed' => $request->input('breed'),
            'approximate_age' => $request->input('approximate_age'),
            'sex' => $request->input('sex'),
            'color' => $request->input('color'),
            'date_of_adoption' => $request->input('date_of_adoption')
        ];
        
        $result = DB::table('adoption_request')->insert($data);

        return redirect()->route('cats.index');
    }

    public function updateStatus($id, Request $request){
        $data = [
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'mobile_phone' => $request->input('mobile_phone'),
            'name_of_cat' => $request->input('name_of_cat'),
            'status' => $request->input('status')
        ];

        $result = DB::table('adoption_request')
                    ->where('id', $id)
                    ->update($data);

        if ($result) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to update entry']);
        }
    }
}
