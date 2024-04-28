<?php

use App\Http\Controllers\CatinfoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

//test route
Route::get('/test', function () {
    return view('test');
});

Route::get('admintest',[CatinfoController::class,'index'])->name('admin.admintest');
Route::get('admintest/create',[CatinfoController::class,'create'])->name('admin.create');
Route::post('admintest',[CatinfoController::class,'store'])->name('admin.store');





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

Route::get('/services', function () {
    return view('services');
})->middleware(['auth', 'verified'])->name('services');

Route::get('events', function () {
    return view('events');
});

Route::get('ContactUs', function(){
    return view ('contactus');
});

Route::get('feed', function(){
    return view ('feed');
});


//Services routes
Route::get('Adopt', function () {
    return view('adopt');
});

Route::get('Report', function () {
    return view('report');
});

Route::get('Donate', function () {
    return view('donate');
});

