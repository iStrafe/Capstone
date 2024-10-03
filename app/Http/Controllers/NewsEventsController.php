<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewsEventsController extends Controller
{
    public function showevents(){
        return view('adminnews.sight');
    }
}
