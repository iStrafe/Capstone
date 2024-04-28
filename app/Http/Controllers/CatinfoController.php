<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CatinfoController extends Controller
{
    public function index(){
        return view('admin.admintest');
    }
    public function create(){
        return view('admin.create');
    }
    public function store(Request $request){
        dd($request);
    }
}
