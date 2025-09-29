@extends('user.master_page')

@section('main')
    @include('user.partials.breadcrumb')
    <section class="tj-blog-section section-gap slidebar-stickiy-container">
        <div class="container">
            <div class="row row-gap-5">
                <div class="col-lg-8">
                    <div class="post-details-wrapper">

                        {{-- Event Banner Image --}}
                        <div class="blog-images wow fadeInUp" data-wow-delay=".1s">
                            <img src="{{ $event->banner_image ? asset('storage/' . $event->banner_image) : asset('frontend/assets/images/event/event-banner.webp') }}"
                                alt="{{ $event->title }}" class="img-fluid">
                        </div>

                        {{-- Event Title --}}
                        <h2 class="title title-anim">{{ $event->title }}</h2>

                        <div class="blog-text">
                            {{-- Event Description --}}
                            @if ($event->short_description)
                                <p class="wow fadeInUp" data-wow-delay=".3s">{{ $event->short_description }}</p>
                            @endif

                            {{-- Loop through dynamic content --}}
                            @foreach ($event->contents as $content)
                                @if ($content->type === 'heading')
                                    <h3 class="wow fadeInUp" data-wow-delay=".3s">
                                        {!! $content->content !!}
                                    </h3>
                                @elseif($content->type === 'paragraph')
                                    <p class="wow fadeInUp" data-wow-delay=".3s">
                                        {!! $content->content !!}
                                    </p>
                                @elseif($content->type === 'list')
                                    @php
                                        $items = json_decode($content->content, true);
                                        $halfCount = ceil(count($items) / 2);
                                        $firstHalf = array_slice($items, 0, $halfCount);
                                        $secondHalf = array_slice($items, $halfCount);
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="wow fadeInUp" data-wow-delay=".3s">
                                                @foreach ($firstHalf as $item)
                                                    <li><span><i class="tji-check"></i></span>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="wow fadeInUp" data-wow-delay=".5s">
                                                @foreach ($secondHalf as $item)
                                                    <li><span><i class="tji-check"></i></span>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @elseif($content->type === 'image')
                                    @php
                                        $images = [];
                                        if ($content->content) {
                                            $decoded = json_decode($content->content, true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                $images = $decoded;
                                            } else {
                                                $images = [$content->content];
                                            }
                                        }
                                    @endphp
                                    <div class="images-wrap">
                                        <div class="row">
                                            @if (count($images) == 1)
                                                <div class="col-sm-12">
                                                    <div class="image-box wow fadeInUp" data-wow-delay=".3s">
                                                        <a class="gallery" data-gall="gallery"
                                                            href="{{ asset('storage/' . $images[0]) }}">
                                                            <img src="{{ asset('storage/' . $images[0]) }}"
                                                                alt="Event Image">
                                                        </a>
                                                    </div>
                                                </div>
                                            @else
                                                @foreach ($images as $index => $image)
                                                    @if ($index == 0)
                                                        <div class="col-sm-12">
                                                            <div class="image-box wow fadeInUp" data-wow-delay=".3s">
                                                                <a class="gallery" data-gall="gallery"
                                                                    href="{{ asset('storage/' . $image) }}">
                                                                    <img src="{{ asset('storage/' . $image) }}"
                                                                        alt="Event Image">
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-sm-6">
                                                            <div class="image-box wow fadeInUp"
                                                                data-wow-delay=".{{ ($index % 2) * 2 + 3 }}s">
                                                                <a class="gallery" data-gall="gallery"
                                                                    href="{{ asset('storage/' . $image) }}">
                                                                    <img src="{{ asset('storage/' . $image) }}"
                                                                        alt="Event Image">
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @elseif($content->type === 'feature')
                                    @php
                                        $feature = json_decode($content->content, true);
                                    @endphp
                                    @if ($loop->first || $event->contents[$loop->index - 1]->type !== 'feature')
                                        <div class="details-content-box">
                                    @endif

                                    <div class="service-details-item wow fadeInUp"
                                        data-wow-delay=".{{ (($loop->index % 3) + 1) * 2 }}s">
                                        @if ($loop->iteration > 1)
                                            <div class="service-number">
                                        @endif
                                        <span
                                            class="number">{{ $feature['number'] ?? sprintf('%02d.', $loop->iteration) }}</span>
                                        <h6 class="title">{{ $feature['title'] ?? '' }}</h6>
                                        <div class="desc">
                                            <p>{{ $feature['description'] ?? '' }}</p>
                                        </div>
                                        @if ($loop->iteration > 1)
                                    </div>
                                @endif
                        </div>

                        @if ($loop->last || $event->contents[$loop->index + 1]->type !== 'feature')
                    </div>
                    @endif
                @elseif($content->type === 'speaker')
                    {{-- Speaker Spotlight Section --}}
                    @php
                        $speakerData = json_decode($content->content, true);
                    @endphp
                    <h3 class="wow fadeInUp" data-wow-delay=".3s">Featured Speakers</h3>
                    <div class="speaker-spotlight-wrap">
                        <div class="row">
                            @foreach ($event->speakers->take(4) as $speaker)
                                <div class="col-md-6 col-lg-3">
                                    <div class="speaker-card wow fadeInUp"
                                        data-wow-delay=".{{ (($loop->index % 4) + 1) * 2 }}s">
                                        @if ($speaker->image)
                                            <div class="speaker-image">
                                                <img src="{{ asset('storage/' . $speaker->image) }}"
                                                    alt="{{ $speaker->name }}">
                                            </div>
                                        @endif
                                        <div class="speaker-info">
                                            <h6 class="speaker-name">{{ $speaker->name }}</h6>
                                            <span class="speaker-title">{{ $speaker->title }}</span>
                                            @if ($speaker->company)
                                                <p class="speaker-company">{{ $speaker->company }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($content->type === 'schedule')
                    {{-- Schedule Section --}}
                    <h3 class="wow fadeInUp" data-wow-delay=".3s">Event Schedule</h3>
                    <div class="schedule-wrap">
                        @foreach ($event->schedules->groupBy('schedule_date') as $date => $daySchedule)
                            <div class="schedule-day wow fadeInUp" data-wow-delay=".3s">
                                <h4 class="day-title">{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</h4>
                                <div class="schedule-items">
                                    @foreach ($daySchedule as $schedule)
                                        <div class="schedule-item">
                                            <div class="schedule-time">
                                                <span>{{ $schedule->formatted_time }}</span>
                                            </div>
                                            <div class="schedule-content">
                                                <h6>{{ $schedule->session_title }}</h6>
                                                @if ($schedule->speaker_name)
                                                    <p class="speaker">Speaker: {{ $schedule->speaker_name }}</p>
                                                @endif
                                                @if ($schedule->location)
                                                    <p class="location">{{ $schedule->location }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif($content->type === 'ticket')
                    {{-- Tickets Section --}}
                    <section class="tj-pricing-section-2 section-gap">
                        <div class="row">
                            <div class="col-12">
                                <div class="sec-heading text-center">
                                    <h3 class="sec-title title-anim">Event <span>Tickets.</span></h3>
                                </div>
                            </div>
                        </div>
                        <div class="row row-gap-4">
                            @foreach ($event->tickets as $ticket)
                                <div class="col-xl-6 col-md-6">
                                    <div class="pricing-box wow fadeInUp" data-wow-delay=".{{ (($loop->index % 3) + 1) * 2 }}s">
                                        <div class="pricing-header">
                                            <h4 class="package-name">{{ $ticket->name }}</h4>
                                            <div class="package-price">
                                                <span class="package-currency">$</span>
                                                <span class="price-number">{{ number_format($ticket->price, 2) }}</span>
                                            </div>
                                            <div class="pricing-btn">
                                                @if ($ticket->is_available)
                                                    <a class="text-btn" href="{{ route('events.register.form', ['event_id' => $event->id, 'ticket_id' => $ticket->id]) }}">
                                                        <span class="btn-text"><span>Buy Ticket</span></span>
                                                        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                                    </a>
                                                @else
                                                    <button class="btn btn-secondary" disabled>Sold Out</button>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="list-items">
                                            @if ($ticket->features)
                                                <ul>
                                                    @foreach ($ticket->features as $feature)
                                                        <li><i class="tji-list"></i>{{ $feature }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @elseif($content->type === 'sponsor')
                    {{-- Sponsors Section --}}
                    <h3 class="wow fadeInUp" data-wow-delay=".3s">Event Sponsors</h3>
                    @foreach (['platinum', 'gold', 'silver', 'bronze'] as $tier)
                        @php
                            $tierSponsors = $event->sponsors->where('tier', $tier);
                        @endphp
                        @if ($tierSponsors->count() > 0)
                            <div class="sponsor-tier-wrap">
                                <h4 class="tier-title">{{ ucfirst($tier) }} Sponsors</h4>
                                <div class="row">
                                    @foreach ($tierSponsors as $sponsor)
                                        <div
                                            class="col-md-{{ $tier == 'platinum' ? '6' : ($tier == 'gold' ? '4' : '3') }}">
                                            <div class="sponsor-card wow fadeInUp"
                                                data-wow-delay=".{{ (($loop->index % 4) + 1) * 2 }}s">
                                                @if ($sponsor->logo)
                                                    <img src="{{ asset('storage/' . $sponsor->logo) }}"
                                                        alt="{{ $sponsor->name }}">
                                                @endif
                                                <h6>{{ $sponsor->name }}</h6>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @endif
                    @endforeach
                </div>

                {{-- Navigation --}}
                <div class="tj-post__navigation mb-0 wow fadeInUp" data-wow-delay=".3s">
                    <div class="tj-nav__post previous">
                        <div class="tj-nav-post__nav prev_post">
                            <a href="#"><span><i class="tji-arrow-left"></i></span>Previous Event</a>
                        </div>
                    </div>
                    <div class="tj-nav-post__grid">
                        <a href="{{ route('events.index') }}"><i class="tji-window"></i></a>
                    </div>
                    <div class="tj-nav__post next">
                        <div class="tj-nav-post__nav next_post">
                            <a href="#">Next Event<span><i class="tji-arrow-right"></i></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="tj-main-sidebar slidebar-stickiy">

                {{-- Event Info Widget --}}
                <div class="tj-sidebar-widget widget-categories wow fadeInUp" data-wow-delay=".1s">
                    <h4 class="widget-title">Event Info</h4>

                    <div class="infos-item">
                        <div class="project-icons">
                            <i class="tji-calendar"></i>
                        </div>
                        <div class="project-text">
                            <span>Date</span>
                            <h6 class="title">{{ $event->start_date->format('M d, Y') }}</h6>
                        </div>
                    </div>

                    <div class="infos-item">
                        <div class="project-icons">
                            <i class="tji-clock"></i>
                        </div>
                        <div class="project-text">
                            <span>Time</span>
                            <h6 class="title">{{ $event->start_date->format('h:i A') }}</h6>
                        </div>
                    </div>

                    <div class="infos-item">
                        <div class="project-icons">
                            <i class="tji-location-2"></i>
                        </div>
                        <div class="project-text">
                            <span>Location</span>
                            <h6 class="title">{{ $event->location }}</h6>
                        </div>
                    </div>

                    @if ($event->venue)
                        <div class="infos-item">
                            <div class="project-icons">
                                <i class="tji-building"></i>
                            </div>
                            <div class="project-text">
                                <span>Venue</span>
                                <h6 class="title">{{ $event->venue }}</h6>
                            </div>
                        </div>
                    @endif

                    <div class="infos-item">
                        <div class="project-icons">
                            <i class="tji-user"></i>
                        </div>
                        <div class="project-text">
                            <span>Organizer</span>
                            <h6 class="title">{{ $event->organizer_name ?? 'Event Team' }}</h6>
                        </div>
                    </div>

                    @if ($event->price)
                        <div class="infos-item">
                            <div class="project-icons">
                                <i class="tji-budget"></i>
                            </div>
                            <div class="project-text">
                                <span>Starting Price</span>
                                <h6 class="title">${{ number_format($event->price, 2) }}</h6>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Quick Registration Widget --}}
                @if ($event->is_upcoming)
                    <div class="tj-sidebar-widget widget-feature-item wow fadeInUp" data-wow-delay=".3s">
                        <div class="feature-box">
                            <div class="feature-content">
                                <h2 class="title">Register</h2>
                                <span>{{ $event->type }}</span>
                                <a class="read-more feature-contact" href="{{ route('events.register', $event->slug) }}">
                                    <i class="tji-ticket"></i>
                                    <span>Book Now</span>
                                </a>
                            </div>
                            <div class="feature-images">
                                <img src="{{ $event->thumbnail ? asset('storage/' . $event->thumbnail) : asset('frontend/assets/images/event/event-promo.webp') }}"
                                    alt="{{ $event->title }}">
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Other Events Widget --}}
                <div class="tj-sidebar-widget service-categories wow fadeInUp" data-wow-delay=".5s">
                    <h4 class="widget-title">More Events</h4>
                    <ul>
                        @foreach (\App\Models\Event::published()->upcoming()->where('id', '!=', $event->id)->take(5)->get() as $otherEvent)
                            <li>
                                <a href="{{ route('events.show', $otherEvent->slug) }}">
                                    {{ $otherEvent->title }}
                                    <span class="icon"><i class="tji-arrow-right"></i></span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        </div>
        </div>
    </section>


@endsection
@section('title', ($event->title ?? 'Event Details') . ' | Forward Edge Consulting')
@push('styles')
    <style>
        /* =================================================================== */
        /* MODERN EVENT UI STYLES */
        /* =================================================================== */

        :root {
            --tj-color-primary: #007bff; /* Blue */
            --tj-color-secondary: #0056b3; /* Darker Blue */
            --tj-color-success: #28a745;
            --tj-color-bg-light: #f8f9fa;
        }

        /* 1. Hero Section Enhancements (Full-bleed banner with overlay) */
        .event-hero {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            margin-bottom: 50px;
            background-color: #333; /* Fallback */
        }

        .event-hero img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            transition: transform 0.6s ease-in-out;
            opacity: 0.9;
        }

        .event-hero:hover img {
            transform: scale(1.03);
            opacity: 1;
        }

        .event-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            top: 50%;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.9));
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .event-meta-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .meta-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: background 0.3s;
        }

        .meta-badge i {
            margin-right: 5px;
        }

        /* 2. Speaker Cards */
        .speaker-card {
            background: var(--tj-color-bg-light);
            border-radius: 16px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1px solid rgba(0, 123, 255, 0.1);
            height: 100%;
        }

        .speaker-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 123, 255, 0.1);
            background: white;
        }

        .speaker-image {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
        }

        .speaker-image img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--tj-color-primary);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        .speaker-info .speaker-name {
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .speaker-info .speaker-title {
            color: var(--tj-color-primary);
            font-size: 0.9rem;
            display: block;
        }

        .speaker-info .speaker-company {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
        }

        /* 3. Schedule Enhancement */
        .schedule-day {
            background: white;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid #eee;
        }

        .day-title {
            background: linear-gradient(135deg, var(--tj-color-primary) 0%, var(--tj-color-secondary) 100%);
            color: white;
            padding: 20px;
            margin: 0;
            font-weight: 600;
            font-size: 1.25rem;
        }

        .schedule-item {
            display: flex;
            align-items: flex-start;
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.3s ease;
        }

        .schedule-item:hover {
            background: var(--tj-color-bg-light);
        }

        .schedule-item:last-child {
            border-bottom: none;
        }

        .schedule-time {
            min-width: 120px;
            max-width: 120px;
            background: rgba(0, 123, 255, 0.1);
            color: var(--tj-color-primary);
            padding: 8px 10px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .schedule-content h6 {
            color: #333;
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }

        .schedule-content .speaker {
            color: var(--tj-color-primary);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .schedule-content .location {
            font-size: 0.85rem;
            color: #6c757d;
        }

        /* 4. Ticket Cards */
        .ticket-card-modern {
            background: white;
            border-radius: 16px;
            padding: 35px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 3px solid transparent;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        /* Rainbow/Gradient top border for premium look */
        .ticket-card-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #007bff, #28a745, #ffc107, #dc3545);
        }

        .ticket-card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .package-name {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 1.4rem;
            color: var(--tj-color-primary);
        }

        .package-price .price-number {
            font-size: 3rem;
            font-weight: 800;
            color: #333;
        }

        .package-price .package-currency {
            font-size: 1.5rem;
            font-weight: 600;
            color: #6c757d;
        }

        .list-items ul {
            list-style: none;
            padding: 0;
            text-align: left;
            margin-top: 20px;
        }

        .list-items li {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
            display: flex;
            align-items: center;
        }

        .list-items li:last-child {
            border-bottom: none;
        }

        .list-items i {
            color: var(--tj-color-success);
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .buy-ticket-btn {
            background: linear-gradient(135deg, var(--tj-color-primary) 0%, var(--tj-color-secondary) 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white !important;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .buy-ticket-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 123, 255, 0.4);
            color: white;
            opacity: 0.9;
        }

        /* 5. Sponsors */
        .sponsor-card-modern {
            background: white;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .sponsor-card-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .sponsor-card-modern img {
            max-height: 70px;
            max-width: 100%;
            object-fit: contain;
            filter: grayscale(100%);
            transition: filter 0.3s ease;
        }

        .sponsor-card-modern:hover img {
            filter: grayscale(0%);
        }

        .tier-title-modern {
            margin: 40px 0 20px 0;
            color: var(--tj-color-primary);
            text-transform: uppercase;
            font-size: 1.1rem;
            letter-spacing: 2px;
            font-weight: 700;
            border-bottom: 2px solid var(--tj-color-primary);
            padding-bottom: 10px;
        }

        /* Other General Enhancements */
        .title-anim {
            font-size: 2.5rem;
            font-weight: 800;
            margin-top: 0;
        }

        .tj-section-title {
            margin-bottom: 40px;
            text-align: center;
        }

        .tj-section-title .title {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .service-details-item {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            border-left: 5px solid var(--tj-color-primary);
        }
    </style>
@endpush
@section('main')
    @include('user.partials.breadcrumb')
    <section class="tj-blog-section section-gap slidebar-stickiy-container">
        <div class="container">
            <div class="row row-gap-5">
                <div class="col-lg-8">
                    <div class="post-details-wrapper">

                        {{-- 1. MODERN EVENT HERO SECTION --}}
                        <div class="event-hero wow fadeInUp" data-wow-delay=".1s">
                            {{-- Assuming $event->banner_image holds the path --}}
                            <img src="{{ $event->banner_image ? asset('storage/' . $event->banner_image) : asset('frontend/assets/images/event/event-banner.webp') }}"
                                alt="{{ $event->title }}" class="img-fluid">

                            <div class="event-overlay">
                                <h1 class="title text-white mb-3 title-anim">{{ $event->title }}</h1>
                                <p class="lead text-white-50">{{ $event->short_description }}</p>

                                <div class="event-meta-badges">
                                    <span class="meta-badge"><i class="tji-calendar"></i>
                                        {{ \Carbon\Carbon::parse($event->start_date)->format('M d') }} -
                                        {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}
                                    </span>
                                    <span class="meta-badge"><i class="tji-location"></i>
                                        {{ $event->location ?? 'Online' }}
                                    </span>
                                    <span class="meta-badge"><i class="tji-tag"></i>
                                        {{ isset($event->base_price) && $event->base_price > 0 ? 'From ₦' . number_format($event->base_price, 0) : 'Free/TBD' }}
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <a href="#tickets-section" class="buy-ticket-btn register-btn">
                                        <i class="tji-ticket me-2"></i> Register Now
                                    </a>
                                </div>
                            </div>
                        </div>


                        <div class="blog-text">
                            <h3 class="wow fadeInUp mb-4" data-wow-delay=".3s">Event Overview</h3>
                            {{-- Event Description --}}
                            @if ($event->short_description)
                                <p class="wow fadeInUp" data-wow-delay=".3s">{{ $event->short_description }}</p>
                            @endif

                            {{-- Loop through dynamic content --}}
                            {{-- FIX: NULL CHECK for $event->contents --}}
                            @if (isset($event->contents) && $event->contents->count())
                                @foreach ($event->contents as $content)
                                    @if ($content->type === 'heading')
                                        <h3 class="wow fadeInUp mt-4" data-wow-delay=".3s">
                                            {!! $content->content !!}
                                        </h3>
                                    @elseif($content->type === 'paragraph')
                                        <p class="wow fadeInUp" data-wow-delay=".3s">
                                            {!! $content->content !!}
                                        </p>
                                    @elseif($content->type === 'list')
                                        @php
                                            // FIX: NULL CHECK for $content->content and decode
                                            $items = json_decode($content->content ?? '[]', true);
                                            $halfCount = ceil(count($items) / 2);
                                            $firstHalf = array_slice($items, 0, $halfCount);
                                            $secondHalf = array_slice($items, $halfCount);
                                        @endphp
                                        @if (count($items) > 0)
                                            <div class="row tj-list style-1 my-4">
                                                <div class="col-md-6">
                                                    <ul class="wow fadeInUp list-unstyled" data-wow-delay=".3s">
                                                        @foreach ($firstHalf as $item)
                                                            <li><i class="tji-check"></i> {{ $item }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <ul class="wow fadeInUp list-unstyled" data-wow-delay=".5s">
                                                        @foreach ($secondHalf as $item)
                                                            <li><i class="tji-check"></i> {{ $item }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                    @elseif($content->type === 'image')
                                        {{-- Image display logic (kept complex as per original, but using the modern structure) --}}
                                        @php
                                            $images = [];
                                            $decoded = json_decode($content->content ?? '[]', true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
                                                $images = $decoded;
                                            } elseif ($content->content) {
                                                $images = [$content->content];
                                            }
                                        @endphp
                                        <div class="images-wrap my-4">
                                            <div class="row">
                                                @if (count($images) == 1)
                                                    <div class="col-sm-12">
                                                        <div class="image-box wow fadeInUp" data-wow-delay=".3s">
                                                            <img src="{{ asset('storage/' . $images[0]) }}"
                                                                alt="Event Image" class="img-fluid rounded shadow-sm">
                                                        </div>
                                                    </div>
                                                @else
                                                    @foreach ($images as $index => $image)
                                                        <div class="col-sm-6 mb-4">
                                                            <div class="image-box wow fadeInUp"
                                                                data-wow-delay=".{{ (($index % 2) + 1) * 2 }}s">
                                                                <img src="{{ asset('storage/' . $image) }}"
                                                                    alt="Event Image" class="img-fluid rounded shadow-sm">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($content->type === 'feature')
                                        {{-- Feature Block Display (re-using service-details-item for a modern look) --}}
                                        @php
                                            $feature = json_decode($content->content ?? '[]', true);
                                            $isFeatureWrapOpen = ($loop->first || $event->contents[$loop->index - 1]->type !== 'feature');
                                            $isFeatureWrapClose = ($loop->last || $event->contents[$loop->index + 1]->type !== 'feature');
                                        @endphp

                                        @if ($isFeatureWrapOpen)
                                            <div class="details-content-box mt-4">
                                        @endif

                                        <div class="service-details-item wow fadeInUp"
                                            data-wow-delay=".{{ (($loop->index % 3) + 1) * 2 }}s">
                                            <div class="service-number">
                                                <span
                                                    class="number">{{ $feature['number'] ?? sprintf('%02d.', $loop->iteration) }}</span>
                                            </div>
                                            <h6 class="title">{{ $feature['title'] ?? 'Feature' }}</h6>
                                            <div class="desc">
                                                <p>{{ $feature['description'] ?? 'Detailed description of the event feature.' }}
                                                </p>
                                            </div>
                                        </div>

                                        @if ($isFeatureWrapClose)
                                            </div>
                                        @endif

                                    @endif
                                @endforeach
                            @endif

                            {{-- 2. SPEAKER SPOTLIGHT SECTION --}}
                            <div class="tj-section-title text-center mt-5" id="speakers-section">
                                <h2 class="title wow fadeInUp" data-wow-delay=".1s">Meet Our Experts</h2>
                            </div>
                            <div class="speaker-spotlight-wrap mb-5">
                                <div class="row row-gap-4">
                                    {{-- FIX: NULL CHECK for $event->speakers --}}
                                    @if (isset($event->speakers) && $event->speakers->count())
                                        @foreach ($event->speakers as $speaker)
                                            <div class="col-xl-3 col-md-6 col-sm-6">
                                                <div class="speaker-card wow fadeInUp"
                                                    data-wow-delay=".{{ (($loop->index % 4) + 1) * 2 }}s">
                                                    <div class="speaker-image">
                                                        <img src="{{ $speaker->image ? asset('storage/' . $speaker->image) : asset('frontend/assets/images/team/speaker-default.webp') }}"
                                                            alt="{{ $speaker->name }}">
                                                    </div>
                                                    <div class="speaker-info">
                                                        <h6 class="speaker-name">{{ $speaker->name }}</h6>
                                                        <span class="speaker-title">{{ $speaker->tagline ?? $speaker->title ?? 'Expert' }}</span>
                                                        @if ($speaker->company)
                                                            <p class="speaker-company">{{ $speaker->company }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-12 text-center text-muted">Speaker line-up to be announced soon.</div>
                                    @endif
                                </div>
                            </div>

                            {{-- 3. SCHEDULE SECTION --}}
                            <div class="tj-section-title text-center mt-5" id="schedule-section">
                                <h2 class="title wow fadeInUp" data-wow-delay=".1s">Full Event Schedule</h2>
                            </div>
                            <div class="schedule-wrap mb-5">
                                {{-- FIX: NULL CHECK for $event->schedules --}}
                                @if (isset($event->schedules) && $event->schedules->count())
                                    {{-- Group schedule items by date --}}
                                    @foreach ($event->schedules->groupBy('schedule_date') as $date => $daySchedule)
                                        <div class="schedule-day wow fadeInUp" data-wow-delay=".3s">
                                            <h4 class="day-title">
                                                {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</h4>
                                            <div class="schedule-items">
                                                @foreach ($daySchedule as $schedule)
                                                    <div class="schedule-item">
                                                        <div class="schedule-time">
                                                            {{-- FIX: Use accessors or proper time formatting --}}
                                                            <span>{{ $schedule->formatted_time ?? \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}</span>
                                                        </div>
                                                        <div class="schedule-content">
                                                            <h6>{{ $schedule->session_title ?? 'Session Title' }}</h6>
                                                            @if ($schedule->speaker_name)
                                                                <p class="speaker">Speaker: {{ $schedule->speaker_name }}
                                                                </p>
                                                            @endif
                                                            @if ($schedule->location)
                                                                <p class="location"><i class="tji-location"></i>
                                                                    {{ $schedule->location }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 text-center text-muted p-4 border rounded">Event schedule will be published shortly.</div>
                                @endif
                            </div>

                            {{-- 4. TICKETS SECTION --}}
                            <div class="tj-section-title text-center mt-5" id="tickets-section">
                                <h2 class="title wow fadeInUp" data-wow-delay=".1s">Secure Your Spot</h2>
                            </div>
                            <div class="row row-gap-4 mb-5">
                                {{-- FIX: NULL CHECK for $event->tickets --}}
                                @if (isset($event->tickets) && $event->tickets->count())
                                    @foreach ($event->tickets as $ticket)
                                        <div class="col-xl-6 col-md-6">
                                            <div class="ticket-card-modern wow fadeInUp"
                                                data-wow-delay=".{{ (($loop->index % 2) + 1) * 2 }}s">
                                                <h4 class="package-name">{{ $ticket->name }}</h4>
                                                <div class="package-price">
                                                    <span class="package-currency">₦</span>
                                                    <span
                                                        class="price-number">{{ number_format($ticket->price ?? 0, 0) }}</span>
                                                </div>

                                                <div class="list-items">
                                                    {{-- FIX: NULL CHECK for $ticket->features and ensure array --}}
                                                    @php $features = is_array($ticket->features) ? $ticket->features : json_decode($ticket->features ?? '[]', true); @endphp
                                                    @if (count($features) > 0)
                                                        <ul>
                                                            @foreach ($features as $feature)
                                                                <li><i class="tji-check"></i>{{ $feature }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </div>

                                                <div class="pricing-btn mt-4">
                                                    @if ($ticket->is_available ?? true)
                                                        <a class="buy-ticket-btn"
                                                            href="{{ route('events.register.form', ['event_id' => $event->id, 'ticket_id' => $ticket->id]) }}">
                                                            <i class="tji-ticket me-2"></i>
                                                            <span>Buy Ticket</span>
                                                        </a>
                                                    @else
                                                        <button class="btn btn-secondary rounded-pill" disabled>Sold
                                                            Out</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 text-center wow fadeInUp">
                                        <p class="lead text-muted">Ticket information coming soon. Please check back later.</p>
                                    </div>
                                @endif
                            </div>

                            {{-- 5. SPONSORS SECTION --}}
                            <div class="tj-section-title text-center mt-5" id="sponsors-section">
                                <h2 class="title wow fadeInUp" data-wow-delay=".1s">Our Esteemed Sponsors</h2>
                            </div>
                            <div class="sponsor-wrap mb-5">
                                {{-- FIX: NULL CHECK for $event->sponsors --}}
                                @if (isset($event->sponsors) && $event->sponsors->count())
                                    {{-- Grouping sponsors by tier --}}
                                    @foreach ($event->sponsors->groupBy('tier') as $tier => $tierSponsors)
                                        <div class="sponsor-tier-wrap">
                                            <h4 class="tier-title-modern text-center">{{ ucfirst($tier) }} Partners</h4>
                                            <div class="row justify-content-center">
                                                @foreach ($tierSponsors as $sponsor)
                                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                                        <div class="sponsor-card-modern wow fadeInUp"
                                                            data-wow-delay=".{{ (($loop->index % 6) + 1) * 1 }}s">
                                                            @if ($sponsor->logo)
                                                                <img src="{{ asset('storage/' . $sponsor->logo) }}"
                                                                    alt="{{ $sponsor->name }}">
                                                            @else
                                                                <h6>{{ $sponsor->name }}</h6>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 text-center text-muted p-4 border rounded">Interested in sponsoring? Contact us!</div>
                                @endif
                            </div>
                        </div>

                        {{-- Navigation --}}
                        <div class="tj-post__navigation mb-0 wow fadeInUp mt-5" data-wow-delay=".3s">
                            <div class="tj-nav__post previous">
                                <div class="tj-nav-post__nav prev_post">
                                    {{-- FIX: Check for existence of $prevEvent --}}
                                    @if (isset($prevEvent))
                                        <a href="{{ route('events.show', $prevEvent->slug) }}"><span><i
                                                    class="tji-arrow-left"></i></span>Previous Event</a>
                                    @else
                                        <span>No Previous Event</span>
                                    @endif
                                </div>
                            </div>
                            <div class="tj-nav-post__grid">
                                <a href="{{ route('events.index') }}"><i class="tji-window"></i></a>
                            </div>
                            <div class="tj-nav__post next">
                                <div class="tj-nav-post__nav next_post">
                                    {{-- FIX: Check for existence of $nextEvent --}}
                                    @if (isset($nextEvent))
                                        <a href="{{ route('events.show', $nextEvent->slug) }}">Next Event<span><i
                                                    class="tji-arrow-right"></i></span></a>
                                    @else
                                        <span>No Next Event</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SIDEBAR --}}
                <div class="col-lg-4">
                    <div class="tj-main-sidebar slidebar-stickiy">

                        {{-- Event Info Widget --}}
                        <div class="tj-sidebar-widget widget-categories wow fadeInUp" data-wow-delay=".1s">
                            <h4 class="widget-title">Event Info</h4>

                            <div class="infos-item">
                                <div class="project-icons"> <i class="tji-calendar"></i> </div>
                                <div class="project-text">
                                    <span>Date</span>
                                    <h6 class="title">{{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                                    </h6>
                                </div>
                            </div>

                            <div class="infos-item">
                                <div class="project-icons"> <i class="tji-clock"></i> </div>
                                <div class="project-text">
                                    <span>Time</span>
                                    <h6 class="title">{{ \Carbon\Carbon::parse($event->start_date)->format('h:i A') }}
                                    </h6>
                                </div>
                            </div>

                            <div class="infos-item">
                                <div class="project-icons"> <i class="tji-location-2"></i> </div>
                                <div class="project-text">
                                    <span>Location</span>
                                    <h6 class="title">{{ $event->location ?? 'Virtual' }}</h6>
                                </div>
                            </div>

                            @if ($event->venue)
                                <div class="infos-item">
                                    <div class="project-icons"> <i class="tji-building"></i> </div>
                                    <div class="project-text">
                                        <span>Venue</span>
                                        <h6 class="title">{{ $event->venue }}</h6>
                                    </div>
                                </div>
                            @endif

                            <div class="infos-item">
                                <div class="project-icons"> <i class="tji-user"></i> </div>
                                <div class="project-text">
                                    <span>Organizer</span>
                                    <h6 class="title">{{ $event->organizer_name ?? 'Forward Edge' }}</h6>
                                </div>
                            </div>

                            @if (isset($event->base_price))
                                <div class="infos-item">
                                    <div class="project-icons"> <i class="tji-budget"></i> </div>
                                    <div class="project-text">
                                        <span>Starting Price</span>
                                        <h6 class="title">₦{{ number_format($event->base_price, 0) }}</h6>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Quick Registration Widget --}}
                        @if ($event->is_upcoming ?? true)
                            <div class="tj-sidebar-widget widget-feature-item wow fadeInUp" data-wow-delay=".3s">
                                <div class="feature-box">
                                    <div class="feature-content">
                                        <h2 class="title">Register</h2>
                                        <span>{{ $event->type ?? 'Training' }}</span>
                                        <a class="read-more buy-ticket-btn" href="#tickets-section">
                                            <i class="tji-ticket"></i>
                                            <span>Book Now</span>
                                        </a>
                                    </div>
                                    <div class="feature-images">
                                        <img src="{{ $event->thumbnail ? asset('storage/' . $event->thumbnail) : asset('frontend/assets/images/event/event-promo.webp') }}"
                                            alt="{{ $event->title }}">
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Other Events Widget --}}
                        <div class="tj-sidebar-widget service-categories wow fadeInUp" data-wow-delay=".5s">
                            <h4 class="widget-title">More Events</h4>
                            <ul>
                                @foreach (\App\Models\Event::published()->upcoming()->where('id', '!=', $event->id)->take(5)->get() as $otherEvent)
                                    <li>
                                        <a href="{{ route('events.show', $otherEvent->slug) }}">
                                            {{ $otherEvent->title }}
                                            <span class="icon"><i class="tji-arrow-right"></i></span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection