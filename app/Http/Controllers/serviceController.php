<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class serviceController extends Controller
{
    public function showServices(){
        return view('Services.servicespage');
    }
    public function showAdopt(){
        return view('Services.adopt');
    }
    public function showDonate(){
        return view('Services.donate');
    }
    public function showreport(){
        return view('Services.report');
    }
}
