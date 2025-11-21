@extends('user.master_page')
@section('title', ($event->title ?? 'Event Details') . ' | Forward Edge Consulting')

@section('main')
    @include('user.partials.breadcrumb')

    <section class="section-gap">
        <div class="container">
            <div class="row g-4 align-items-center mb-5">
                <div class="col-lg-6">
                    <img src="{{ $event->banner_image ? asset('storage/' . $event->banner_image) : ($event->thumbnail ? asset('storage/' . $event->thumbnail) : asset('frontend/assets/images/project/project-6.webp')) }}"
                         alt="{{ $event->title }}"
                         class="img-fluid rounded-4 shadow">
                </div>
                <div class="col-lg-6">
                    <span class="badge rounded-pill bg-primary-subtle text-primary mb-3 text-capitalize">
                        {{ $event->type ?? 'event' }}
                    </span>
                    <h1 class="fw-bold mb-3">{{ $event->title }}</h1>
                    <p class="text-muted mb-4">{{ $event->short_description }}</p>

                    <div class="row gy-3">
                        <div class="col-md-6">
                            <strong class="d-block text-uppercase small text-muted">Location</strong>
                            <span>{{ $event->venue ? $event->venue . ' • ' : '' }}{{ $event->location }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong class="d-block text-uppercase small text-muted">Dates</strong>
                            <span>
                                {{ optional($event->start_date)->format('M d, Y') ?? 'TBA' }}
                                @if($event->end_date)
                                    – {{ optional($event->end_date)->format('M d, Y') }}
                                @endif
                            </span>
                        </div>
                        <div class="col-md-6">
                            <strong class="d-block text-uppercase small text-muted">Price</strong>
                            <span>{{ $event->price ? '₦' . number_format($event->price, 2) : 'Free' }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong class="d-block text-uppercase small text-muted">Organizer</strong>
                            <span>{{ $event->organizer_name ?? 'Forward Edge Consulting' }}</span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="{{ route('events.register.form', $event->slug) }}" class="tj-primary-btn">
                            <span class="btn-text"><span>Register now</span></span>
                            <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                        </a>
                        @if($event->page?->slug)
                            <a href="{{ route('page.show', $event->page->slug) }}" class="tj-secondary-btn" target="_blank">
                                <span class="btn-text"><span>View landing page</span></span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h3 class="fw-bold mb-3">About this event</h3>
                            <p>{{ $event->short_description ?? 'Stay tuned for more information about this event.' }}</p>
                            <p>
                                Follow us for updates or reach out if you need help securing a seat.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-3">Need help?</h4>
                            <p class="text-muted mb-2">We’re happy to assist with group registrations or sponsorship enquiries.</p>
                            <ul class="list-unstyled mb-4">
                                <li><i class="bi bi-envelope me-2 text-primary"></i>{{ $event->organizer_email ?? 'events@forwardedge.com' }}</li>
                                <li><i class="bi bi-telephone me-2 text-primary"></i>{{ $event->contact_phone ?? '+234 800 000 0000' }}</li>
                            </ul>
                            <a href="{{ route('contact') }}" class="btn btn-outline-primary w-100">Contact our team</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
