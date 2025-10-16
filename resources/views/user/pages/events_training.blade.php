@extends('user.master_page')
@section('title', ' Events and Training | Forward Edge Consulting')
@section('main')
    @include('user.partials.breadcrumb')
    <section class="tj-project-section section-gap">
        <div class="container">
            <div class="row row-gap-4">
                @forelse ($events as $event)
                    <div class="col-xl-4 col-md-6">
                        <div class="project-item wow fadeInUp" data-wow-delay=".1s">
                            <div class="project-img" style="height:420px; overflow:hidden;">
                                @if ($event->thumbnail)
                                    <img src="{{ asset('storage/' . $event->thumbnail) }}" alt="{{ $event->title }}"
                                        style="width:100%; height:100%; object-fit:cover; object-position:center;">
                                @else
                                    <img src="{{ asset('frontend/assets/images/project/project-6.webp') }}"
                                        alt="Default Image">
                                @endif
                            </div>
                            <div class="project-content">
                                <span class="categories"><a href="javascript:void(0)">{{ $event->type }}</a></span>
                                <div class="project-text">
                                    <h4 class="title">
                                        <a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a>
                                    </h4>
                                    <a class="project-btn" href="{{ route('events.show', $event->slug) }}"><i
                                            class="tji-arrow-right-big"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-wrap">
                            <!-- EVENTS SVG -->
                            <svg class="empty-svg" viewBox="0 0 400 260" role="img" aria-label="New events coming soon">
                                <defs>
                                    <linearGradient id="gb" x1="0" y1="0" x2="1"
                                        y2="1">
                                        <stop offset="0%" stop-color="#FDB714" />
                                        <stop offset="100%" stop-color="#2c99d4" />
                                    </linearGradient>
                                    <linearGradient id="gbSoft" x1="0" y1="0" x2="1"
                                        y2="1">
                                        <stop offset="0%" stop-color="#FDB714" stop-opacity=".15" />
                                        <stop offset="100%" stop-color="#2c99d4" stop-opacity=".15" />
                                    </linearGradient>
                                </defs>
                                <!-- soft blob background -->
                                <ellipse cx="200" cy="170" rx="160" ry="70" fill="url(#gbSoft)" />
                                <!-- calendar -->
                                <rect x="90" y="40" rx="14" ry="14" width="220" height="150"
                                    fill="#fff" stroke="url(#gb)" stroke-width="3" />
                                <rect x="90" y="70" width="220" height="30" fill="url(#gb)" opacity=".1" />
                                <circle cx="140" cy="55" r="8" fill="url(#gb)" />
                                <circle cx="260" cy="55" r="8" fill="url(#gb)" />
                                <!-- smile + confetti -->
                                <circle cx="200" cy="130" r="34" fill="none" stroke="url(#gb)"
                                    stroke-width="3" />
                                <circle cx="187" cy="125" r="4" fill="#2c99d4" />
                                <circle cx="213" cy="125" r="4" fill="#2c99d4" />
                                <path d="M186 140q14 12 28 0" fill="none" stroke="#FDB714" stroke-width="3"
                                    stroke-linecap="round" />
                                <g opacity=".9">
                                    <path d="M120 95l8-8" stroke="#FDB714" stroke-width="3" stroke-linecap="round" />
                                    <path d="M280 95l8-8" stroke="#2c99d4" stroke-width="3" stroke-linecap="round" />
                                    <circle cx="305" cy="120" r="3" fill="#FDB714" />
                                    <circle cx="105" cy="120" r="3" fill="#2c99d4" />
                                </g>
                                <!-- title -->
                                <text x="200" y="210" text-anchor="middle" font-family="Inter, ui-sans-serif"
                                    font-size="18" fill="#222">
                                    New events coming soon
                                </text>
                            </svg>
                            <p class="empty-text">We’re lining up fresh bootcamp events. Check back shortly!</p>
                            <a href="{{ route('contact') }}" class="btn btn-gradient">Notify me</a>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- ✅ Dynamic Pagination --}}
            <div class="tj-pagination d-flex justify-content-center mt-4">
                {{ $events->links('vendor.pagination.custom') }}
            </div>
            {{-- <div class="tj-pagination d-flex justify-content-center">
              <ul>
                <li>
                  <span aria-current="page" class="page-numbers current">1</span>
                </li>
                <li>
                  <a class="page-numbers" href="#">2</a>
                </li>
                <li>
                  <a class="page-numbers" href="#">3</a>
                </li>
                <li>
                  <a class="next page-numbers" href="#"><i class="tji-arrow-right-long"></i></a>
                </li>
              </ul>
            </div> --}}
        </div>
    </section>
@endsection
