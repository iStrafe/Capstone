<?php

use App\Http\Controllers\CatinfoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\serviceController;
use Illuminate\Support\Facades\Route;



//test route
Route::get('/test', function () {
    return view('test');
});

Route::get('admin',function(){
    return view('admintest');
})->middleware(['auth'])->name('admin');

Route::get('userDashboard',function(){
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

//About us Route
Route::get('/aboutus', function () {
    return view('aboutus');
})->name('aboutus');

//Home Route
Route::get('/', function () {
    return view('home');
})->name('home');

//Events Route
Route::get('/events', function () {
    return view('events');
})->name('events');

//Contact page route
Route::get('/ContactUs', function () {
    return view('ContactUs');
})->name('contactus');

//Feed page route
Route::get('/feed', function () {
    return view('feed');
})->name('feed');

//======== views/admin controllers =======
Route::get('admintest/create',[CatinfoController::class,'index'])->name('admin.index');
Route::get('admintest/create',[CatinfoController::class,'create']);
Route::post('admintest/create',[CatinfoController::class,'store'])->name('admin.store');
Route::get('admintest/create',[CatinfoController::class,'viewCatInformation2'])->name('catinfo.view');

Route::get('admintest/search', [CatinfoController::class, 'search'])->name('admin.create');


//======= services controllers ======
Route::get('Services/report',[CatinfoController::class,'reportpage']);
Route::post('Services/report',[CatinfoController::class,'report'])->name('admin.report');
//Route::get('admintest/create',[CatinfoController::class,'viewReportInformation'])->name('reportinfo.view');

Route::middleware('auth')->group(function () {

    
    Route::get('dashboard',[CatinfoController::class,'viewCatInformation'])->name('dashboard');
    
   
});



Route::get('/homecopy', function () {
    return view('homecopy');
});


// ====== logged-in user homepage
//Route::get('/home', function () {
    //return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

//Authentication
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';

//============= Navbar Routes =============== 

//About-us page
//Route::get('/aboutus', function () {
   // return view('aboutus');
//})->middleware(['auth', 'verified'])->name('aboutus');


//Service Page
Route::middleware('auth')->group(function () {


    //Service page routes
    Route::get('/services',[serviceController::class,'showServices']);
    Route::get('/adopt',[serviceController::class,'showAdopt']);
    Route::get('/donate',[serviceController::class,'showDonate']);
    Route::get('/report',[serviceController::class,'showReport']);
   
});

//News & Events page routes


//Service page
/*
Route::group(['prefix' => 'service'],function(){

    Route::get('/',[serviceController::class,'showServices']);

})->middleware(['auth', 'verified'])->name('services');*/

