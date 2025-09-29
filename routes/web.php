<?php

use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\AjaxAuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Static Pages
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('user.pages.welcome');
})->name('home');

Route::get('/about', function () {
    return view('user.pages.about');
})->name('about');

Route::get('/contact', function () {
    return view('user.pages.contact');
})->name('contact');

Route::get('/gallery', function () {
    return view('user.pages.gallery');
})->name('gallery');

/*
|--------------------------------------------------------------------------
| Content Routes (Blog, Services, etc.)
|--------------------------------------------------------------------------
*/
// Blog routes
Route::prefix('blog')->name('blog')->group(function () {
    Route::get('/', [BlogController::class, 'index']);
    Route::get('/{slug}', [BlogController::class, 'show'])->name('s.show');
});

// Service routes  
Route::prefix('services')->name('services')->group(function () {
    Route::get('/', [ServiceController::class, 'ServiceList']);
    Route::get('/{slug}', [ServiceController::class, 'show'])->name('.show');
});

/*
|--------------------------------------------------------------------------
| Academy & Course Routes
|--------------------------------------------------------------------------
*/
// Academy/Course listing and details
Route::get('/academy', [CourseController::class, 'getCourse'])->name('academy');
Route::get('/course/{slug}', [CourseController::class, 'showdetails'])->name('course.show');

// Shop (course products)
Route::prefix('shop')->name('shop')->group(function () {
    Route::get('/', [CourseController::class, 'shop']);
    Route::get('/{slug}', [CourseController::class, 'shopDetails'])->name('.details');
});

/*
|--------------------------------------------------------------------------
| Event Routes
|--------------------------------------------------------------------------
*/
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/upcoming', [EventController::class, 'upcoming'])->name('upcoming');
    Route::get('/featured', [EventController::class, 'featured'])->name('featured');
    Route::get('/{slug}', [EventController::class, 'show'])->name('show');

    // Event registration routes
    Route::get('/{event_id}/register/{ticket_id}', [EventController::class, 'register'])
        ->name('register.form');
    Route::post('/register', [EventController::class, 'submitRegistration'])
        ->name('register');
});

/*
|--------------------------------------------------------------------------
| Cart & Wishlist Routes (Public but require auth for backend)
|--------------------------------------------------------------------------
*/
// Cart routes
Route::prefix('user/cart')->name('user.cart.')->group(function () {
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::get('/json', [CartController::class, 'getCartJson'])->name('json');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});

// Wishlist routes
Route::prefix('user/wishlist')->name('user.wishlist.')->group(function () {
    Route::post('/add', [WishlistController::class, 'add'])->name('add');
    Route::post('/remove', [WishlistController::class, 'remove'])->name('remove');
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::get('/json', [WishlistController::class, 'getwishlistJson'])->name('json');
    Route::get('/count', [WishlistController::class, 'count'])->name('count');
});

/*
|--------------------------------------------------------------------------
| AJAX Authentication Routes
|--------------------------------------------------------------------------
*/
Route::prefix('ajax')->name('ajax.')->group(function () {
    Route::post('/register', [AjaxAuthController::class, 'register'])->name('register');
    Route::post('/login', [AjaxAuthController::class, 'login'])->name('login');
    Route::post('/send-otp', [AjaxAuthController::class, 'sendOtp'])->name('sendOtp');
    Route::post('/verify-otp', [AjaxAuthController::class, 'verifyOtp'])->name('verifyOtp');
    Route::post('/forgot-password', [AjaxAuthController::class, 'forgotPassword'])->name('forgotPassword');
    Route::post('/reset-password', [AjaxAuthController::class, 'resetPassword'])->name('resetPassword');
});

// Password reset view
Route::get('/reset-password', function () {
    return view('auth.reset');
})->name('password.reset');

/*
|--------------------------------------------------------------------------
| Payment Routes (Public callbacks)
|--------------------------------------------------------------------------
*/
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/callback', [PaymentController::class, 'callback'])->name('callback');
    Route::post('/webhook', [PaymentController::class, 'webhook'])->name('webhook');
    Route::get('/success', [OrderController::class, 'success'])->name('success');
    Route::get('/failed', [OrderController::class, 'cancel'])->name('failed');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Profile management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Course enrollment
    Route::prefix('enroll')->name('enroll.')->group(function () {
        Route::get('/price/{schedule}', [EnrollmentController::class, 'pricingPage'])->name('pricing');
        Route::post('/store', [EnrollmentController::class, 'store'])->name('store');
    });

    // Checkout/Order management
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::post('/store', [OrderController::class, 'store'])->name('store');
    });
});

/*
|--------------------------------------------------------------------------
| Admin/Dashboard Routes (if needed)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('admin.pages.dashboard');
})->middleware(['auth'])->name('dashboard');

// Show form
Route::get('/add-services', function () {
    return view('admin.pages.services.add_service');
})->name('services.add');

Route::post('/services/store', [AdminServiceController::class, 'store'])->name('services.store');

Route::get('/admin/courses/create',function () {
        return view('admin.pages.courses.add_course');})->name('courses.create'); // returns view we've built
Route::post('/admin/courses', [CourseController::class, 'store'])->name('courses.store');

// routes/web.php
Route::prefix('admin/events')->group(function () {
    // main events
    Route::get('/', [AdminEventController::class, 'index'])->name('events.index');
    Route::get('/create', [AdminEventController::class, 'create'])->name('events.create');
    Route::post('/', [AdminEventController::class, 'store'])->name('events.store');
    Route::get('/{event}/dashboard', [AdminEventController::class, 'dashboard'])->name('events.dashboard');
    Route::get('/{event}/edit', [AdminEventController::class, 'edit'])->name('events.edit');
    Route::put('/{event}', [AdminEventController::class, 'update'])->name('events.update');
    Route::delete('/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');

    // sub resources
    Route::post('/{event}/contents', [AdminEventController::class, 'storeContent'])->name('events.contents.store');
    Route::delete('/{event}/contents/{content}', [AdminEventController::class, 'destroyContent'])->name('events.contents.destroy');

    Route::post('/{event}/phases', [AdminEventController::class, 'storePhase'])->name('events.phases.store');
    Route::delete('/{event}/phases/{phase}', [AdminEventController::class, 'destroyPhase'])->name('events.phases.destroy');

    Route::post('/{event}/topics/{phase}', [AdminEventController::class, 'storeTopic'])->name('events.topics.store');
    Route::delete('/{event}/topics/{topic}', [AdminEventController::class, 'destroyTopic'])->name('events.topics.destroy');

    Route::post('/{event}/schedules', [AdminEventController::class, 'storeSchedule'])->name('events.schedules.store');
    Route::delete('/{event}/schedules/{schedule}', [AdminEventController::class, 'destroySchedule'])->name('events.schedules.destroy');

    Route::post('/{event}/tickets', [AdminEventController::class, 'storeTicket'])->name('events.tickets.store');
    Route::delete('/{event}/tickets/{ticket}', [AdminEventController::class, 'destroyTicket'])->name('events.tickets.destroy');

    Route::post('/{event}/speakers', [AdminEventController::class, 'storeSpeaker'])->name('events.speakers.store');
    Route::delete('/{event}/speakers/{speaker}', [AdminEventController::class, 'destroySpeaker'])->name('events.speakers.destroy');

    Route::post('/{event}/sponsors', [AdminEventController::class, 'storeSponsor'])->name('events.sponsors.store');
    Route::delete('/{event}/sponsors/{sponsor}', [AdminEventController::class, 'destroySponsor'])->name('events.sponsors.destroy');
});

/*
|--------------------------------------------------------------------------
| Laravel Breeze Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
