<?php

use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminDashboard;
use App\Http\Controllers\Admin\AdminEmailController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\Admin\AdminEnrollmentController;
use App\Http\Controllers\Admin\AdminMessageController;
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
use App\Http\Controllers\PageBuilderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NewsletterController;
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
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
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

    Route::get('/scholarships/apply/course/{course}', [ScolarshipApplicationController::class, 'registerForCourse'])
        ->name('scholarships.apply.course');

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
        Route::get('/price', [EnrollmentController::class, 'pricingPage'])->name('pricing');
        Route::post('/store', [EnrollmentController::class, 'store'])->name('store');
    });

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

    Route::prefix('scholarships')->name('admin.scholarships.')->group(function () {
        Route::get('/applications', [AdminEnrollmentController::class, 'applications'])->name('applications');
        Route::post('/applications/{application}/approve', [AdminEnrollmentController::class, 'approve'])->name('approve');
        Route::post('/applications/{application}/reject', [AdminEnrollmentController::class, 'reject'])->name('reject');
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

        Route::post('/{content}/phases', [AdminCourseController::class, 'storePhase'])
            ->name('phases.store');

        Route::put('/{content}/phases/{phase}', [AdminCourseController::class, 'updatePhase'])
            ->name('phases.update');

        Route::delete('/{content}/phases/{phase}', [AdminCourseController::class, 'destroyPhase'])
            ->name('phases.destroy');

        // Topics under a specific Phase (which belongs to that Content)
        Route::post('/{content}/phases/{phase}/topics', [AdminCourseController::class, 'storeTopic'])
            ->name('topics.store');

        Route::put('/{content}/phases/{phase}/topics/{topic}', [AdminCourseController::class, 'updateTopic'])
            ->name('topics.update');

        Route::delete('/{content}/phases/{phase}/topics/{topic}', [AdminCourseController::class, 'destroyTopic'])
            ->name('topics.destroy');
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
   


    Route::get('/messages', [AdminMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}/json', [AdminMessageController::class, 'showJson'])->name('messages.json');
    Route::post('/messages/{message}/reply', [AdminMessageController::class, 'reply'])->name('messages.reply');

    /*
    |--------------------------------------------------------------------------
    | Admin - Email Engine
    |--------------------------------------------------------------------------
    */
    Route::prefix('emails')->name('admin.emails.')->group(function () {
        Route::get('/contacts', [AdminEmailController::class, 'contacts'])->name('contacts');

        Route::get('/campaigns', [AdminEmailController::class, 'campaignsIndex'])->name('campaigns.index');
        Route::get('/campaigns/create', [AdminEmailController::class, 'campaignsCreate'])->name('campaigns.create');
        Route::post('/campaigns', [AdminEmailController::class, 'campaignsStore'])->name('campaigns.store');
        Route::get('/campaigns/{campaign}', [AdminEmailController::class, 'campaignsShow'])->name('campaigns.show');
        Route::post('/campaigns/{campaign}/send', [AdminEmailController::class, 'campaignsSend'])->name('campaigns.send');
        Route::post('/campaigns/{campaign}/retry', [AdminEmailController::class, 'campaignsRetry'])->name('campaigns.retry');
    });




    Route::prefix('page-builder')->group(function () {
        Route::get('/',                 [PageBuilderController::class, 'adminPages'])->name('pb.pages');
        Route::get('/create',           [PageBuilderController::class, 'createPage'])->name('pb.pages.create');
        Route::post('/store',           [PageBuilderController::class, 'storePage'])->name('pb.pages.store');
        Route::get('/{page}/edit',      [PageBuilderController::class, 'editPage'])->name('pb.pages.edit');
        Route::put('/{page}',           [PageBuilderController::class, 'updatePage'])->name('pb.pages.update');
        Route::delete('/{page}',        [PageBuilderController::class, 'destroyPage'])->name('pb.pages.destroy');

        // Blocks (the one-page screen uses this index)
        Route::get('/{page}/blocks',    [PageBuilderController::class, 'adminBlocks'])->name('pb.blocks');
        Route::post('/{page}/blocks',   [PageBuilderController::class, 'storeBlock'])->name('pb.blocks.store');

        // Edit/save are still used by the one-page form when you click “Edit”
        Route::put('/block/{block}',    [PageBuilderController::class, 'updateBlock'])->name('pb.blocks.update');
        Route::delete('/block/{block}', [PageBuilderController::class, 'destroyBlock'])->name('pb.blocks.destroy');

        // Drag-sort autosave
        Route::post('/{page}/blocks/reorder', [PageBuilderController::class, 'reorderBlocks'])->name('pb.blocks.reorder');
    });
});

Route::get('/p/{slug}', [PageBuilderController::class, 'show'])->name('page.show');
/*
|--------------------------------------------------------------------------
| Laravel Breeze Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
