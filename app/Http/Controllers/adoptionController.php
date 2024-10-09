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

        return redirect()->route('user.cats.index');
    }
}
