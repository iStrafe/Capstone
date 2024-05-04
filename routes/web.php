<?php

use App\Http\Controllers\CatinfoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\serviceController;
use Illuminate\Support\Facades\Route;



//test route
Route::get('/test', function () {
    return view('test');
});

//======== views/admin controllers =======
Route::get('admintest/create',[CatinfoController::class,'index'])->name('admin.index');
Route::get('admintest/create',[CatinfoController::class,'create']);
Route::post('admintest/create',[CatinfoController::class,'store'])->name('admin.store');

//======= services controllers ======
Route::get('Services/report',[CatinfoController::class,'reportpage']);
Route::post('Services/report',[CatinfoController::class,'report'])->name('admin.report');







Route::get('/homecopy', function () {
    return view('homecopy');
});

// ===== Not logged-in user home page
Route::get('/', function () {
    return view('home');
});

// ====== logged-in user homepage
Route::get('/home', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admintest', function () {
    return view('admin.admintest');
})->middleware(['auth', 'verified'])->name('admintest');

//Authentication
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';

//============= Navbar Routes =============== 

//About-us page
Route::get('/aboutus', function () {
    return view('aboutus');
})->middleware(['auth', 'verified'])->name('aboutus');


//Service Page
Route::middleware('auth')->group(function () {


    //Service page routes
    Route::get('/services',[serviceController::class,'showServices']);
    Route::get('/adopt',[serviceController::class,'showAdopt']);
    Route::get('/donate',[serviceController::class,'showDonate']);
    Route::get('/report',[serviceController::class,'showReport']);
   
});

//News & Events page routes
Route::get('/events', function () {
    return view('events');
})->middleware(['auth', 'verified'])->name('events');

//Contact page routes
Route::get('/ContactUs', function () {
    return view('ContactUs');
})->middleware(['auth', 'verified'])->name('contactus');

//Feed page routes
Route::get('/feed', function () {
    return view('feed');
})->middleware(['auth', 'verified'])->name('feed');


//Service page
/*
Route::group(['prefix' => 'service'],function(){

    Route::get('/',[serviceController::class,'showServices']);

})->middleware(['auth', 'verified'])->name('services');*/

