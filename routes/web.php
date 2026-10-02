<?php

use App\Http\Controllers\Admin\AdoptionRequestController as AdminAdoptionRequestController;
use App\Http\Controllers\Admin\CatController as AdminCatController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\AdoptionController;
use App\Http\Controllers\CatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\NewsEventController;
use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public pages
Route::get('/', [CatController::class, 'home'])->name('home');
Route::get('adoptCat', [CatController::class, 'index'])->name('adoptCat');
Route::get('/cat/{cat}', [CatController::class, 'show'])->whereNumber('cat')->name('cats.show');
// The old user dashboard duplicated Home's gallery; old links, bookmarks and intended URLs land on Home.
Route::redirect('userDashboard', '/')->name('dashboard');

Route::get('/aboutus', [PageController::class, 'about'])->name('aboutus');
Route::get('/ContactUs', [PageController::class, 'contact'])->name('contactus');
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
    // Adoption requests need an account so they can be tied to the applicant
    // Guests who follow "Log in to adopt" land here after logging in or signing up.
    Route::get('/cat/{cat}/adopt', [AdoptionController::class, 'start'])->whereNumber('cat')->name('adoption.start');
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

    // Adoption requests: tabs by status, one page per request with its valid IDs
    Route::get('/admin/requests', [AdminAdoptionRequestController::class, 'index'])->name('admin.requests.index');
    Route::get('/admin/requests/{adoptionRequest}', [AdminAdoptionRequestController::class, 'show'])->name('admin.requests.show');
    Route::post('/update-status/{adoptionRequest}', [AdminAdoptionRequestController::class, 'updateStatus'])->name('adoption-request.status');
    Route::get('/valid-ids/{filename}', [AdminAdoptionRequestController::class, 'showValidIdFile'])->name('validIdFile');
    Route::get('/adoption-request/pdf/{adoptionRequest}', [AdminAdoptionRequestController::class, 'generatePDF'])->name('adoption-request.pdf');
    Route::get('/AdoptionRequest', [AdminAdoptionRequestController::class, 'legacyList'])->name('AdoptionRequest');
    Route::get('/ReleasedRequest', [AdminAdoptionRequestController::class, 'legacyReleased'])->name('ReleasedRequest');
    Route::get('/view-valid-ids/{adoptionRequest}', [AdminAdoptionRequestController::class, 'legacyValidIds'])->name('viewValidIds');

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
