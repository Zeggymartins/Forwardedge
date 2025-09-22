@extends('user.master_page')
@section('title', ' Events and Training | Forward Edge Consulting')
@push('styles')
    <style>
        .speaker-card {
            text-align: center;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .speaker-image img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        .schedule-day {
            margin-bottom: 40px;
        }

        .day-title {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }

        .schedule-item {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .schedule-time {
            min-width: 120px;
            font-weight: bold;
            color: #007bff;
        }

        .ticket-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin-bottom: 20px;
        }

        .ticket-price {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
            margin: 10px 0;
        }

        .ticket-features {
            text-align: left;
            margin: 20px 0;
        }

        .sponsor-card {
            text-align: center;
            padding: 20px;
            margin-bottom: 20px;
        }

        .sponsor-card img {
            max-height: 80px;
            max-width: 100%;
            object-fit: contain;
        }

        .tier-title {
            margin: 30px 0 20px 0;
            color: #666;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
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
                        <a href="{{ route('events') }}"><i class="tji-window"></i></a>
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