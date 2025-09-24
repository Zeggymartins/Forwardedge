<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AjaxAuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('user.pages.welcome');
})->name('home');

Route::get('/about', function () {
    return view('user.pages.about');
})->name('about');

// Route::get('/academy', function () {
//     return view('user.pages.academy');
// })->name('academy');

Route::get('/services', function () {
    return view('user.pages.service');
})->name('services');



Route::get('/shop', function () {
    return view('user.pages.shop');
})->name('shop');

// Route::get('/shop-details', function () {
//     return view('user.pages.shop_details');
// })->name('shop.details');

Route::get('/contact', function () {
    return view('user.pages.contact');
})->name('contact');

Route::get('/gallery', function () {
    return view('user.pages.gallery');
})->name('gallery');



Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/blogs/{slug}', [BlogController::class, 'show'])->name('blogs.show');

Route::get('/services', [ServiceController::class, 'ServiceList'])->name('services');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/academy', [CourseController::class, 'getCourse'])->name('academy');
Route::get('/course/{slug}', [CourseController::class, 'showdetails'])->name('course.show');

Route::get('/events', [EventController::class, 'index'])->name('events');
Route::get('/shop', [CourseController::class, 'shop'])->name('shop');
Route::get('/shop/{slug}', [CourseController::class, 'shopDetails'])->name('shop.details');

Route::prefix('events')->name('events.')->group(function () {
    Route::get('/upcoming', [EventController::class, 'upcoming'])->name('upcoming');
    Route::get('/featured', [EventController::class, 'featured'])->name('featured');
    Route::get('/{slug}', [EventController::class, 'show'])->name('show');
    Route::get('{event_id}/register/{ticket_id}', [EventController::class, 'register'])->name('register.form');
    Route::post('/register', [EventController::class, 'submitRegistration'])->name('register');
});

Route::post('/user/cart/add', [CartController::class, 'add'])->name('user.cart.add');
Route::post('/user/cart/remove', [CartController::class, 'remove'])->name('user.cart.remove');
Route::get('/user/cart', [CartController::class, 'index'])->name('user.cart.index');
Route::get('/user/cart/json', [CartController::class, 'getCartJson'])->name('user.cart.json');
Route::get('/user/cart/count', [CartController::class, 'count'])->name('user.cart.count');


Route::post('/user/wishlist/add', [WishlistController::class, 'add'])->name('user.wishlist.add');
Route::post('/user/wishlist/remove', [WishlistController::class, 'remove'])->name('user.wishlist.remove');
Route::get('/user/wishlist/json', [WishlistController::class, 'getwishlistJson'])->name('user.wishlist.json');
Route::get('/user/wishlist', [WishlistController::class, 'index'])->name('user.wishlist.index');
Route::get('/user/wishlist/count', [WishlistController::class, 'count'])->name('user.wishlist.count');

Route::post('/ajax/register', [AjaxAuthController::class, 'register'])->name('ajax.register');
Route::post('/ajax/login', [AjaxAuthController::class, 'login'])->name('ajax.login');
Route::post('/ajax/send-otp', [AjaxAuthController::class, 'sendOtp'])->name('ajax.sendOtp');
Route::post('/ajax/verify-otp', [AjaxAuthController::class, 'verifyOtp'])->name('ajax.verifyOtp');


Route::middleware('auth')->group(function () {
    Route::get('/enroll/price/{schedule}', [EnrollmentController::class, 'pricingPage'])->name('enroll.pricing');
    Route::post('/enroll/store', [EnrollmentController::class, 'store'])->name('enroll.store');
});
Route::middleware('auth')->group(function () {
    Route::post('/checkout/store', [OrderController::class, 'store'])->name('checkout.store');
});

// Public routes
Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
Route::get('/payment/success', [OrderController::class, 'success'])->name('payment.success');
Route::get('/payment/failed', [OrderController::class, 'cancel'])->name('payment.failed');
// Public routes (no auth required)
Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';





