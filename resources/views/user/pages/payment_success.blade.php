@extends('user.master_page')
@section('title', 'Payment Successful | Forward Edge Consulting')
@section('breadcrumb_text', 'Your enrollment is locked in. We just emailed your receipt and course access instructions.')

@section('main')
    @include('user.partials.breadcrumb')

    @php
        // Get the order details from the reference
        $payment = null;
        $order = null;
        $purchasedCourses = collect();

        if (isset($reference)) {
            $payment = \App\Models\Payment::where('reference', $reference)->first();
            if ($payment && $payment->payable instanceof \App\Models\Orders) {
                $order = $payment->payable;
                $order->load('items.course.contents');

                foreach ($order->items as $item) {
                    if ($item->course) {
                        $purchasedCourses->push($item->course);
                    }
                }
            }
        }

        $isGmail = auth()->check() &&
            (str_ends_with(strtolower(auth()->user()->email), '@gmail.com') ||
             str_ends_with(strtolower(auth()->user()->email), '@googlemail.com'));
    @endphp

    <section class="section-gap">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-body text-center py-5 px-4 px-md-5">
                            <div class="mb-4">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success"
                                     style="width:96px;height:96px;">
                                    <i class="tji-check" style="font-size:2.5rem;"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-3">Payment completed!</h2>
                            <p class="text-muted mb-4">
                                Thank you for trusting Forward Edge. We're getting everything ready—check your inbox for a
                                confirmation email with download links and Drive access.
                            </p>
                            @isset($reference)
                                <p class="mb-4 small text-uppercase text-muted">Reference: <span class="fw-semibold text-dark">{{ $reference }}</span></p>
                            @endisset

                            {{-- Show purchased courses with access links --}}
                            @if($purchasedCourses->isNotEmpty())
                                <div class="text-start bg-light rounded-3 p-4 mb-4">
                                    <h5 class="fw-bold mb-3"><i class="fas fa-book-open me-2 text-warning"></i>Your Purchased Courses</h5>
                                    @foreach($purchasedCourses as $course)
                                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                            <span class="fw-medium">{{ $course->title }}</span>
                                            @if($course->contents->isNotEmpty())
                                                <a href="{{ route('student.courses.content', $course->id) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-play-circle me-1"></i>Access Content
                                                </a>
                                            @else
                                                <span class="badge bg-secondary">Content coming soon</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                @if($isGmail)
                                    <div class="alert alert-info text-start">
                                        <i class="fas fa-google me-2"></i>
                                        <strong>Gmail Account Detected!</strong><br>
                                        <small>Your course content will open directly in Google Drive for the best experience.</small>
                                    </div>
                                @endif
                            @endif

                            <div class="d-flex flex-wrap gap-3 justify-content-center">
                                @if($purchasedCourses->isNotEmpty() && $purchasedCourses->first()->contents->isNotEmpty())
                                    <a href="{{ route('student.courses.content', $purchasedCourses->first()->id) }}" class="tj-primary-btn">
                                        <span class="btn-text"><span>Access Your Course</span></span>
                                        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                    </a>
                                @else
                                    <a href="{{ route('academy') }}" class="tj-primary-btn">
                                        <span class="btn-text"><span>Explore more programs</span></span>
                                        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                    </a>
                                @endif
                                <a href="{{ route('shop') }}" class="tj-secondary-btn">
                                    <span class="btn-text"><span>Browse modules</span></span>
                                </a>
                            </div>
                        </div>
                        <div class="bg-success-subtle text-center py-3">
                            <small class="text-success fw-semibold">
                                Didn't receive the email? Check spam or <a href="{{ route('contact') }}">contact us</a>.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
