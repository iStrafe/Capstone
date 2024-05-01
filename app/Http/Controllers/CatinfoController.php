<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CatInfo;
use Illuminate\Support\Facades\DB;

class CatinfoController extends Controller
{
    public function index(){
        $displayData = DB::table('catinfo')->get();
        return view('admin.admintest',['displayData' => $displayData]);
    }
    public function create(){
        return view('admin.create');
    }
    public function store(Request $request){
        //dd($request);
        $data = [
                    'name' => $request->input('title'),
                    'gender' => $request->input('gender'),
                    'breed' => $request->input('breed'),
                    'eye_color' => $request->input('eye_color'),
                    'fur_color' => $request->input('fur_color'),
                    'description' => $request->input('description'),
                    'status' => $request->input('status')
                ];

        $result = DB::table('catinfo')->insert($data);

        return redirect()->route('admin.store');
    }
    public function deleteInfo(Request $request){
        
    }
}
