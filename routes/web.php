<?php

use App\Http\Controllers\Admin\CatController as AdminCatController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\AdoptionController;
use App\Http\Controllers\CatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\NewsEventController;
use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public pages
Route::get('/', [CatController::class, 'home'])->name('home');
Route::get('adoptCat', [CatController::class, 'index'])->name('adoptCat');
Route::get('/cat/{cat}', [CatController::class, 'show'])->whereNumber('cat')->name('cats.show');

Route::get('/aboutus', function () {
    return view('aboutus');
})->name('aboutus');

Route::get('/ContactUs', function () {
    return view('contactus');
})->name('contactus');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:10,1')->name('contact.store');

Route::get('/events', [NewsEventController::class, 'index3'])->name('news-events.events');
// Registered before the admin news-events resource so {news_event} doesn't swallow it
Route::get('/news-events/events', [NewsEventController::class, 'index3'])->name('news-events.index3');

// PayMongo donation
Route::post('/payment', [PaymentController::class, 'createPayment'])->middleware('throttle:10,1')->name('paymongo.create');

// Google Authentication
Route::get('auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google-auth');
Route::get('auth/google/callbacks', [GoogleAuthController::class, 'callbackGoogle']);

// Signed-in users
Route::middleware('auth')->group(function () {
    Route::get('userDashboard', [CatController::class, 'dashboard'])->name('dashboard');

    // Adoption requests need an account so they can be tied to the applicant
    Route::post('/AdoptionForm', [AdoptionController::class, 'create'])->middleware('throttle:10,1')->name('adoption.request');
    Route::get('/myRequest', [AdoptionController::class, 'showMyRequests'])->name('myRequest');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/analyzeImage', [OpenAIController::class, 'showUploadForm']);
    Route::post('/analyzeImage', [OpenAIController::class, 'analyzeImage'])->name('analyze.image');

    Route::get('/AdoptionRequest', [AdoptionController::class, 'showAdoptionRequest'])->name('AdoptionRequest');
    Route::get('/ReleasedRequest', [AdoptionController::class, 'showReleased'])->name('ReleasedRequest');
    Route::post('/update-status/{adoptionRequest}', [AdoptionController::class, 'updateStatus'])->name('adoption-request.status');

    Route::get('/view-valid-ids/{adoptionRequest}', [AdoptionController::class, 'viewValidIds'])->name('viewValidIds');
    Route::get('/valid-ids/{filename}', [AdoptionController::class, 'showValidIdFile'])->name('validIdFile');
    Route::get('/adoption-request/pdf/{adoptionRequest}', [AdoptionController::class, 'generatePDF'])->name('adoption-request.pdf');

    // Cat inventory
    Route::get('/adminDashboard', [AdminCatController::class, 'index']);
    Route::get('/cat', [AdminCatController::class, 'index'])->name('cats.index');
    Route::patch('/admin/cats/{cat}/archive', [AdminCatController::class, 'archive'])->name('admin.cats.archive');
    Route::get('/admin/cats/archived', [AdminCatController::class, 'archived'])->name('admin.cats.archived');
    Route::patch('/admin/cats/{cat}/restore', [AdminCatController::class, 'restore'])->name('admin.cats.restore');

    // Messages sent through the Contact Us form
    Route::get('/admin/messages', [ContactMessageController::class, 'index'])->name('admin.messages.index');
    Route::patch('/admin/messages/{contact}/handled', [ContactMessageController::class, 'handled'])->name('admin.messages.handled');
    Route::delete('/admin/messages/{contact}', [ContactMessageController::class, 'destroy'])->name('admin.messages.destroy');
    Route::prefix('adminDashboard')->name('admin.')->group(function () {
        Route::resource('cats', AdminCatController::class);
    });

    Route::resource('/news-events', NewsEventController::class);
});

require __DIR__.'/auth.php';
