<?php

use App\Http\Controllers\CatinfoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\serviceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

//test route
Route::get('/test', function () {
    return view('test');
});

Route::get('admintest',[CatinfoController::class,'index'])->name('admin.admintest');
Route::get('admintest/create',[CatinfoController::class,'create']);
Route::post('admintest/create',[CatinfoController::class,'store'])->name('admin.store');





Route::get('/homecopy', function () {
    return view('homecopy');
});

Route::get('/home', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admintest', function () {
    return view('admin.admintest');
})->middleware(['auth', 'verified'])->name('admintest');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';

//Navbar Routes
Route::get('aboutus', function () {
    return view('aboutus');
});


//Service Page
Route::middleware('auth')->group(function () {


    //Service page routes
    Route::get('/services',[serviceController::class,'showServices']);
    Route::get('/adopt',[serviceController::class,'showAdopt']);
    Route::get('/donate',[serviceController::class,'showDonate']);
    Route::get('/report',[serviceController::class,'showReport']);
   
});

Route::get('events', function () {
    return view('events');
});

Route::get('ContactUs', function(){
    return view ('contactus');
});

Route::get('feed', function(){
    return view ('feed');
});
Route::get('header', function(){
    return view ('header');
});




//Service page
/*
Route::group(['prefix' => 'service'],function(){

    Route::get('/',[serviceController::class,'showServices']);

})->middleware(['auth', 'verified'])->name('services');*/

