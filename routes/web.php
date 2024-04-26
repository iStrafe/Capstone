<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('services', function () {
    return view('services');
});

Route::get('Adopt', function () {
    return view('adopt');
});

Route::get('Report', function () {
    return view('report');
});

Route::get('Donate', function () {
    return view('donate');
});

Route::get('ContactUs', function(){
    return view ('contactus');
});
