<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\CatController;
use App\Http\Controllers\adoptionController;
use App\Http\Controllers\CatinfoController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\serviceController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use SebastianBergmann\CodeCoverage\Driver\Driver;

//Cat Adoption

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('cats', CatController::class);
});




//Paymongo Payment
Route::get('/payment',[PaymentController::class,'paymentView']);
Route::post('/payment', [PaymentController::class, 'createPayment'])->name('paymongo.create');
/*
Route::get('/home',[PaymentController::class,'paymentView']);
Route::post('/home', [PaymentController::class, 'createPayment'])->name('paymongo.create.home');*/

//Google Authentication
Route::get('auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google-auth');
Route::get('auth/google/callbacks', [GoogleAuthController::class,'callbackGoogle']);

//Session Timeout route
Route::group(['middleware' => ['web', \App\Http\Middleware\SessionTimeout::class]], function () {
    Route::get('/home', function () {
        return view('home');
    });
});

//Rout for logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

//Route for Image verification

//test route
Route::get('/test', function () {
    return view('test');
});

//Route for admin upon login
Route::get('admin',function(){
    return view('admintest');
})->middleware(['auth'])->name('admin');

Route::get('adminDashboard',function(){
    return view('admin.admintest');
});


//Route for Users upon login
Route::get('userDashboard',function(){
    if(!Auth::user()){
        redirect('/');
    }
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

//========admin controllers =======
Route::get('admintestDashboard',[CatinfoController::class,'index'])->name('admin.index');
Route::get('admintestDashboard',[CatinfoController::class,'create']);
Route::post('admintestDashboard',[CatinfoController::class,'store'])->name('admin.store');
Route::get('admintestDashboard',[CatinfoController::class,'viewCatInformation2'])->name('catinfo.view');

Route::get('admintest/search', [CatinfoController::class, 'search'])->name('admin.create');


//======= services controllers ======
Route::get('Services/report',[CatinfoController::class,'reportpage']);
Route::post('Services/report',[CatinfoController::class,'report'])->name('admin.report');
//Route::get('admintest/create',[CatinfoController::class,'viewReportInformation'])->name('reportinfo.view');

Route::middleware('auth')->group(function () {

    
    Route::get('dashboard',[CatinfoController::class,'viewCatInformation'])->name('dashboard');
    
   
});


//Testing route
Route::get('/homecopy', function () {
    return view('homecopy');
});


// ====== logged-in user homepage
//Route::get('/home', function () {
    //return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

//Authentication - User profile management 
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});





//============= Navbar Routes =============== 

//About-us page
//Route::get('/aboutus', function () {
   // return view('aboutus');
//})->middleware(['auth', 'verified'])->name('aboutus');


//Service Page
Route::middleware('auth')->group(function () {


    //Service page routes
    Route::get('/AdoptionForm',[adoptionController::class,'showAdoptionForm'])->name('AdoptionForm');
    Route::post('/AdoptionForm',[adoptionController::class,'create'])->name('adoption.request');
 
   
});

//News & Events page routes


//Service page
/*
Route::group(['prefix' => 'service'],function(){

    Route::get('/',[serviceController::class,'showServices']);

})->middleware(['auth', 'verified'])->name('services');*/

require __DIR__.'/auth.php';
