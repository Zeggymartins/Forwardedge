<?php

use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminDashboard;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\Admin\AdminEnrollmentController;
use App\Http\Controllers\Admin\AdminTransactionsController;
use App\Http\Controllers\AjaxAuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScolarshipApplicationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\WishlistController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use App\Models\Scholarship;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Public Static Pages
|--------------------------------------------------------------------------
*/

Route::get('/', [WelcomeController::class, 'index'])->name('home');
Route::get('/about', function () {
    return view('user.pages.about');
})->name('about');

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

Route::get('/contact', [MessageController::class, 'create'])->name('contact');
Route::post('/contact', [MessageController::class, 'store'])->name('contact.store');
Route::get('/gallery', [GalleryController::class, 'getPhotos'])->name('gallery');

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
    Route::get('/data', [CourseController::class, 'shopData'])->name('.data');
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

    Route::get('/scholarships/apply/{schedule}', [ScolarshipApplicationController::class, 'Register'])
        ->name('scholarships.apply');

    // Submit application
    Route::post('/scholarships/apply/{schedule}', [ScolarshipApplicationController::class, 'storeData'])
        ->name('scholarships.apply.store');
   Route::get('/scholarships', [ScolarshipApplicationController::class, 'display'])
        ->name('scholarships');
// routes/web.php
Route::get('/scholarships/thank-you', fn() => view('user.pages.thankyou', [
    'course'   => session('thankyou.course'),   // optional: pass context
    'schedule' => session('thankyou.schedule'),
    'enrollUrl' => session('thankyou.enrollUrl'),
    'specializationsUrl' => session('thankyou.specializationsUrl'),
]))->name('scholarships.thankyou');

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
| Payment Routes (Public callbacks for Paystack)
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
Route::middleware(['auth', 'role:user'])->group(function () {
    // Course enrollment
    Route::prefix('enroll')->name('enroll.')->group(function () {
        Route::get('/price/{schedule}', [EnrollmentController::class, 'pricingPage'])->name('pricing');
        Route::post('/store', [EnrollmentController::class, 'store'])->name('store');
    });
    // Route::get('/scholarships/apply/{schedule}', [ScolarshipApplicationController::class, 'create'])
    //     ->whereNumber('schedule')->name('scholarships.apply');
    // Route::post('/scholarships/apply/{schedule}', [ScolarshipApplicationController::class, 'store'])
    //     ->whereNumber('schedule')->name('scholarships.store');
    // Checkout/Order management
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::post('/store', [OrderController::class, 'store'])->name('store');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes - Secured with Custom Prefix
| Change "ctrl-panel-v2" to something unique and memorable for your team
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('ctrl-panel-v2')->group(function () {

    // Dashboard (Login Landing)
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Profile management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin - Services Management
    |--------------------------------------------------------------------------
    */
    Route::get('/services/add', function () {
        return view('admin.pages.services.add_service');
    })->name('admin.services.add');

    Route::prefix('services')->name('admin.services.')->group(function () {
        Route::get('/', [AdminServiceController::class, 'index'])->name('index');
        Route::post('/', [AdminServiceController::class, 'store'])->name('store');
        Route::get('/{id}', [AdminServiceController::class, 'show'])->name('show');
        Route::put('/{id}', [AdminServiceController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminServiceController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/contents', [AdminServiceController::class, 'storeContent'])->name('contents.store');
        Route::put('/{serviceId}/contents/{contentId}', [AdminServiceController::class, 'updateContent'])->name('contents.update');
        Route::delete('/{serviceId}/contents/{contentId}', [AdminServiceController::class, 'destroyContent'])->name('contents.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin - Events Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('events')->name('admin.events.')->group(function () {
        // Main events
        Route::get('/', [AdminEventController::class, 'index'])->name('list');
        Route::get('/create', [AdminEventController::class, 'create'])->name('create');
        Route::post('/', [AdminEventController::class, 'store'])->name('store');
        Route::get('/{event}/dashboard', [AdminEventController::class, 'dashboard'])->name('dashboard');
        Route::put('/{event}', [AdminEventController::class, 'update'])->name('update');
        Route::delete('/{event}', [AdminEventController::class, 'destroy'])->name('destroy');
        Route::get('/registrations', [AdminEventController::class, 'Registrations'])->name('registrations');

        // Event Contents
        Route::post('/{event}/contents', [AdminEventController::class, 'storeContent'])->name('contents.store');
        Route::put('/contents/{content}', [AdminEventController::class, 'updateContent'])->name('contents.update');
        Route::delete('/contents/{content}', [AdminEventController::class, 'destroyContent'])->name('contents.destroy');

        // Tickets
        Route::post('/{event}/tickets', [AdminEventController::class, 'storeTicket'])->name('tickets.store');
        Route::put('/tickets/{ticket}', [AdminEventController::class, 'updateTicket'])->name('tickets.update');
        Route::delete('/tickets/{ticket}', [AdminEventController::class, 'destroyTicket'])->name('tickets.destroy');

        // Speakers
        Route::post('/{event}/speakers', [AdminEventController::class, 'storeSpeaker'])->name('speakers.store');
        Route::put('/speakers/{speaker}', [AdminEventController::class, 'updateSpeaker'])->name('speakers.update');
        Route::delete('/speakers/{speaker}', [AdminEventController::class, 'destroySpeaker'])->name('speakers.destroy');

        // Schedules
        Route::post('/{event}/schedules', [AdminEventController::class, 'storeSchedule'])->name('schedules.store');
        Route::put('/schedules/{schedule}', [AdminEventController::class, 'updateSchedule'])->name('schedules.update');
        Route::delete('/schedules/{schedule}', [AdminEventController::class, 'destroySchedule'])->name('schedules.destroy');

        // Sponsors
        Route::post('/{event}/sponsors', [AdminEventController::class, 'storeSponsor'])->name('sponsors.store');
        Route::put('/sponsors/{sponsor}', [AdminEventController::class, 'updateSponsor'])->name('sponsors.update');
        Route::delete('/sponsors/{sponsor}', [AdminEventController::class, 'destroySponsor'])->name('sponsors.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin - Enrollments Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('enrollments')->name('admin.enrollments.')->group(function () {
        Route::get('/', [AdminEnrollmentController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminEnrollmentController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin - Blogs Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('blogs')->name('admin.blogs.')->group(function () {
        Route::get('/', [AdminBlogController::class, 'index'])->name('index');
        Route::get('/create', [AdminBlogController::class, 'create'])->name('create');
        Route::post('/', [AdminBlogController::class, 'store'])->name('store');
        Route::get('/{id}', [AdminBlogController::class, 'show'])->name('show');
        Route::put('/{id}', [AdminBlogController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminBlogController::class, 'destroy'])->name('destroy');

        // Blog Details
        Route::post('/{id}/details', [AdminBlogController::class, 'storeDetail'])->name('details.store');
        Route::put('/{blogId}/details/{detailId}', [AdminBlogController::class, 'updateDetail'])->name('details.update');
        Route::delete('/{blogId}/details/{detailId}', [AdminBlogController::class, 'destroyDetail'])->name('details.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin - Courses Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('courses')->name('admin.courses.')->group(function () {
        Route::get('/', [AdminCourseController::class, 'index'])->name('index');
        Route::get('/create', [AdminCourseController::class, 'create'])->name('create');
        Route::post('/', [AdminCourseController::class, 'store'])->name('store');
        Route::get('/{id}/dashboard', [AdminCourseController::class, 'dashboard'])->name('dashboard');
        Route::put('/{id}', [AdminCourseController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminCourseController::class, 'destroy'])->name('destroy');

        // Course Contents
        Route::post('/{id}/details', [AdminCourseController::class, 'storeDetails'])->name('details.store');
        Route::put('/{courseId}/contents/{contentId}', [AdminCourseController::class, 'updateDetails'])->name('details.update');
        Route::delete('/{courseId}/contents/{contentId}', [AdminCourseController::class, 'destroyDetails'])->name('details.destroy');

        // Course Phases
        Route::post('/{id}/phases', [AdminCourseController::class, 'storePhase'])->name('phases.store');
        Route::put('/{courseId}/phases/{phaseId}', [AdminCourseController::class, 'updatePhase'])->name('phases.update');
        Route::delete('/{courseId}/phases/{phaseId}', [AdminCourseController::class, 'destroyPhase'])->name('phases.destroy');

        // Course Topics
        Route::post('/{courseId}/phases/{phaseId}/topics', [AdminCourseController::class, 'storeTopic'])->name('topics.store');
        Route::put('/{courseId}/phases/{phaseId}/topics/{topicId}', [AdminCourseController::class, 'updateTopic'])->name('topics.update');
        Route::delete('/{courseId}/phases/{phaseId}/topics/{topicId}', [AdminCourseController::class, 'destroyTopic'])->name('topics.destroy');

        // Course Schedules
        Route::post('/{id}/schedules', [AdminCourseController::class, 'storeSchedule'])->name('schedules.store');
        Route::put('/{courseId}/schedules/{scheduleId}', [AdminCourseController::class, 'updateSchedule'])->name('schedules.update');
        Route::delete('/{courseId}/schedules/{scheduleId}', [AdminCourseController::class, 'destroySchedule'])->name('schedules.destroy');

        // TESTIMONIALS (index is per-course; store is global with course_id in form)
        Route::get('/testimonials', [AdminCourseController::class, 'testimonialsIndex'])
            ->name('testimonials.index');

        Route::post('testimonials', [AdminCourseController::class, 'testimonialsStore'])
            ->name('testimonials.store');

        Route::put('{course}/testimonials/{testimonial}', [AdminCourseController::class, 'testimonialsUpdate'])
            ->name('testimonials.update');

        Route::delete('{course}/testimonials/{testimonial}', [AdminCourseController::class, 'testimonialsDestroy'])
            ->name('testimonials.destroy');

        // FAQS (same idea)
        Route::get('/faqs', [AdminCourseController::class, 'faqsIndex'])
            ->name('faqs.index');

        Route::post('faqs', [AdminCourseController::class, 'faqsStore'])
            ->name('faqs.store');

        Route::put('{course}/faqs/{faq}', [AdminCourseController::class, 'faqsUpdate'])
            ->name('faqs.update');

        Route::delete('{course}/faqs/{faq}', [AdminCourseController::class, 'faqsDestroy'])
            ->name('faqs.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin - Course Contents (Separate Management)
    |--------------------------------------------------------------------------
    */
    Route::prefix('course-contents')->name('admin.course_contents.')->group(function () {
        Route::get('/', [AdminCourseController::class, 'courseContent'])->name('index');
        Route::post('/', [AdminCourseController::class, 'storeContent'])->name('store');
        Route::put('/{courseContent}', [AdminCourseController::class, 'updateContent'])->name('update');
        Route::delete('/{courseContent}', [AdminCourseController::class, 'destroyContent'])->name('destroy');
        Route::get('/{courseId}', [AdminCourseController::class, 'showContent'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin - Transactions & Orders Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('transactions')->name('admin.transactions.')->group(function () {
        Route::get('/', [AdminTransactionsController::class, 'index'])->name('index');
        Route::get('/{transaction}', [AdminTransactionsController::class, 'show'])->name('show');
    });

    Route::get('/orders', [AdminTransactionsController::class, 'getOrders'])->name('admin.orders.show');

    /*
    |--------------------------------------------------------------------------
    | Admin - FAQs Management
    |--------------------------------------------------------------------------
    */
    Route::resource('faqs', FaqController::class)
        ->except(['create', 'edit', 'show'])
        ->names([
            'index' => 'admin.faqs.index',
            'store' => 'admin.faqs.store',
            'update' => 'admin.faqs.update',
            'destroy' => 'admin.faqs.destroy',
        ]);

    /*
    |--------------------------------------------------------------------------
    | Admin - Gallery Management
    |--------------------------------------------------------------------------
    */
    Route::resource('gallery', GalleryController::class)
        ->except(['create', 'edit', 'show'])
        ->names([
            'index' => 'admin.gallery.index',
            'store' => 'admin.gallery.store',
            'update' => 'admin.gallery.update',
            'destroy' => 'admin.gallery.destroy',
        ]);
    Route::get('/scholarships', [ScolarshipApplicationController::class, 'index'])
        ->name('scholarships.index');

    Route::get('/scholarships/create', [ScolarshipApplicationController::class, 'create'])
        ->name('scholarships.create');

    Route::post('/scholarships', [ScolarshipApplicationController::class, 'store'])
        ->name('scholarships.store');

    Route::get('/scholarships/{scholarship}', [ScolarshipApplicationController::class, 'show'])
        ->name('scholarships.show');

    Route::get('/scholarships/{scholarship}/edit', [ScolarshipApplicationController::class, 'edit'])
        ->name('scholarships.edit');

    Route::put('/scholarships/{scholarship}', [ScolarshipApplicationController::class, 'update'])
        ->name('scholarships.update');

    Route::delete('/scholarships/{scholarship}', [ScolarshipApplicationController::class, 'destroy'])
        ->name('scholarships.destroy');

    Route::put('/debug/scholarships/{scholarship}', function (Request $r, Scholarship $scholarship) {
        $file = $r->file('hero_image');

        dd([
            // session / csrf
            'session_id'        => $r->session()->getId(),
            'has_cookie'        => $r->hasCookie(config('session.cookie')),
            'cookie_name'       => config('session.cookie'),
            'csrf_input'        => $r->input('_token'),
            'csrf_header'       => $r->header('X-CSRF-TOKEN'),
            'session_csrf'      => $r->session()->token(),

            // request / headers
            'method'            => $r->method(),
            'content_length'    => $r->server('CONTENT_LENGTH'),
            'content_type'      => $r->header('Content-Type'),
            'referer'           => $r->header('Referer'),
            'origin'            => $r->header('Origin'),
            'host'              => $r->getHost(),
            'full_url'          => $r->fullUrl(),

            // inputs (avoid dumping huge arrays)
            'keys'              => array_keys($r->all()),
            'slug'              => $r->input('slug'),
            'status'            => $r->input('status'),

            // file info
            'has_file'          => $r->hasFile('hero_image'),
            'file_ok'           => $file ? $file->isValid() : null,
            'file_name'         => $file?->getClientOriginalName(),
            'file_mime'         => $file?->getMimeType(),
            'file_size_bytes'   => $file?->getSize(),

            // server limits (if accessible)
            'ini_upload_max'    => ini_get('upload_max_filesize'),
            'ini_post_max'      => ini_get('post_max_size'),
        ]);
    })->withoutMiddleware(VerifyCsrfToken::class)->name('debug.scholarships.update');
});


/*
|--------------------------------------------------------------------------
| Laravel Breeze Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
