<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scholarship</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('frontend/assets/images/fav.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/font-awesome-pro.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bexon-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/venobox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/odometer-theme-default.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/meanmenu.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/shop.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/main.css') }}" />
</head>

<body>

    {{-- Header(s) (unchanged branding) --}}
    {{-- <header class="header-area header-1 h6-header header-absolute section-gap-x">
    <div class="container-fluid">
      <div class="row p-3">
        <div class="col-12"><div class="header-wrapper">
          <div class="site_logo">
            <a class="logo" href="/"><img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Logo"></a>
          </div>
        </div></div>
      </div>
    </div>
  </header>

  <header class="header-area header-1 h6-header header-duplicate header-sticky section-gap-x">
    <div class="container-fluid">
      <div class="row p-3">
        <div class="col-12"><div class="header-wrapper">
          <div class="site_logo">
            <a class="logo" href="/"><img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Logo"></a>
          </div>
        </div></div>
      </div>
    </div>
  </header> --}}

    <div id="smooth-wrapper">
        <div id="smooth-content">
@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    /** @var \App\Models\Scholarship $scholarship */
    $course = $scholarship->course ?? null;

    // HERO
    $heroTitle   = $scholarship->headline ?: ($course->title ?? 'Scholarship');
    $heroCtaText = $scholarship->closing_cta_text ?: 'Apply Now';

    // Closing URL: prefer scholarship CTA, else course page, else '#'
    $closingUrl  = $scholarship->closing_cta_url
                 ?: (optional($course)->slug ? route('course.show', $course->slug) : '#');

    // Image: use accessor if you have one; else build storage path or fallback
    $heroImage   = method_exists($scholarship, 'getImageUrlAttribute') && !empty($scholarship->image_url)
                 ? $scholarship->image_url
                 : ($scholarship->image ? asset('storage/'.$scholarship->image)
                                         : asset('frontend/assets/images/hero/h6-hero-banner.webp'));

    // Dates
    $startDate   = $scholarship->opens_at;
    $endDate     = $scholarship->closes_at;
    $startYear   = $startDate ? Carbon::parse($startDate)->year : null;
    $startLabel  = $startDate ? Carbon::parse($startDate)->format('M j, Y') : null;
    $endLabel    = $endDate ? Carbon::parse($endDate)->format('M j, Y') : null;

    // Subtext
    $historyDesc = $scholarship->subtext ?: 'Recognized by industry leaders, our award-winning team delivers excellence.';

    // PROGRAM INCLUDES
    $dummyImgs = [
        asset('frontend/assets/images/service/service1.jpeg'),
        asset('frontend/assets/images/service/service2.jpeg'),
        asset('frontend/assets/images/service/service3.jpeg'),
    ];
    $resolveProgramImage = function ($img, $i) use ($dummyImgs) {
        if (empty($img)) return $dummyImgs[$i % count($dummyImgs)];
        return Str::startsWith($img, ['http://','https://','/']) ? $img : asset('storage/'.$img);
    };
    $programIncludes = collect($scholarship->program_includes ?? [])
        ->map(function ($raw) {
            if (is_array($raw)) {
                return [
                    'title' => trim((string)($raw['title'] ?? '')),
                    'image' => $raw['image'] ?? null,
                ];
            }
            return ['title' => trim((string)$raw), 'image' => null];
        })
        ->filter(fn($it) => $it['title'] !== '')
        ->values();

    // ABOUT
    $about       = (string)($scholarship->about ?? '');
    $aboutKicker = $course->title ?? 'Forward Edge';
    $ctaText     = $scholarship->closing_cta_text ?: 'Apply Now';
    $aboutImage  = asset('frontend/assets/images/service/service7.jpeg');


    // HOW TO APPLY
    $howToApply = collect($scholarship->how_to_apply ?? [])
        ->map(fn($it) => is_array($it) ? trim((string)($it['title'] ?? '')) : trim((string)$it))
        ->filter(fn($s) => $s !== '')
        ->values();

    // WHO CAN APPLY
    $whoCanApply = collect($scholarship->who_can_apply ?? [])
        ->map(fn($it) => is_array($it) ? trim((string)($it['title'] ?? '')) : trim((string)$it))
        ->filter(fn($s) => $s !== '')
        ->values();

    // WHY / NOTES / CLOSING
    $whyHeading       = 'Why Apply';
    $whyText          = Str::limit(strip_tags($about), 220)
                        ?: 'Gain hands-on experience, expert mentorship, and access to opportunities that accelerate your growth.';
    $important_note   = (string)($scholarship->important_note ?? '');
    $closing_headline = $scholarship->closing_headline ?: 'Ready to take the next step?';
    $closing_cta_text = $scholarship->closing_cta_text ?: 'Apply Now';

    // TESTIMONIALS (optional relation on Course)
    $courseTestimonials = collect(optional($course)->testimonials ?? []);
    $avatar = function ($path) {
        if (!$path) return asset('frontend/assets/images/avatar-placeholder.png');
        return Str::startsWith($path, ['http://','https://','/']) ? $path : asset('storage/'.$path);
    };
@endphp


<main id="primary" class="site-main">
    <div class="top-space-15"></div>

    {{-- ========== Banner / Hero ========== --}}
    <section class="tj-banner-section h6-hero section-gap-x">
        <div class="banner-area">
            <div class="banner-left-box">
                <div class="banner-content">
                    <h1 class="banner-title title-anim">{{ $heroTitle }}</h1>

                    <div class="btn-area wow fadeInUp" data-wow-delay=".8s">
                        <a class="tj-primary-btn" href="{{ $closingUrl }}">
                            <span class="btn-text"><span>{{ $heroCtaText }}</span></span>
                            <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                        </a>
                    </div>

                    <div class="h6-hero-bottom wow fadeInLeft" data-wow-delay=".9s">
                        <div class="h6-hero-history">
                            @if ($startYear)
                                <h4 class="h6-hero-history-title mt-3">{{ $startYear }}</h4>
                            @endif
                            @if (!empty($historyDesc))
                                <p class="h6-hero-history-desc">{{ $historyDesc }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="banner-right-box">
                <div class="banner-img wow fadeInUp" data-wow-delay=".3s">
                    <img data-speed=".8" src="{{ $heroImage }}" alt="Hero Banner">
                </div>
            </div>
        </div>
    </section>

    {{-- ========== Program Includes (Slider) ========== --}}
    <section class="h6-service section-gap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-heading sec-heading-centered style-2 style-6">
                        <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
                            <i class="tji-box"></i>OUR PROGRAM
                        </span>
                        <h2 class="sec-title title-anim">Program Includes:</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="h6-service-slider swiper">
                        <div class="swiper-wrapper">
                            @forelse ($programIncludes as $i => $item)
                                @php
                                    $title = $item['title'] ?? '';
                                    $img   = $resolveProgramImage($item['image'] ?? null, $i);
                                    $idx   = str_pad($i + 1, 2, '0', STR_PAD_LEFT) . '.';
                                @endphp
                                <div class="swiper-slide">
                                    <div class="h6-service-item">
                                        <div class="h6-service-thumb">
                                            <a href="{{ $closingUrl }}"><img src="{{ $img }}" alt="{{ $title }}"></a>
                                        </div>
                                        <div class="h6-service-content">
                                            <h5 class="h6-service-index">{{ $idx }}</h5>
                                            <div class="h6-service-title-wrap">
                                                <h4 class="title">
                                                    <a href="{{ $closingUrl }}">{{ $title }}</a>
                                                </h4>
                                                <a class="text-btn" href="{{ $closingUrl }}">
                                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                {{-- Empty state card --}}
                                <div class="swiper-slide">
                                    <div class="h6-service-item">
                                        <div class="h6-service-thumb">
                                            <img src="{{ $resolveProgramImage(null, 0) }}" alt="Program feature">
                                        </div>
                                        <div class="h6-service-content">
                                            <h5 class="h6-service-index">01.</h5>
                                            <div class="h6-service-title-wrap">
                                                <h4 class="title">Details coming soon</h4>
                                                <a class="text-btn" href="{{ $closingUrl }}">
                                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <div class="swiper-pagination-area"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== About ========== --}}
    <section class="tj-about-section h6-about section-gap section-gap-x">
        <div class="container">
            <div class="row">
                <div class="col-xl-6 col-lg-6">
                    <div class="about-content-area h6-about-content style-1 wow fadeInLeft" data-wow-delay=".2s">
                        <div class="sec-heading style-2 style-6">
                            <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
                                <i class="tji-box"></i>{{ $aboutKicker }}
                            </span>
                            <h2 class="sec-title title-anim">
                                Cybersecurity, IT & Renewable Energy Solutions for a Secure and Resilient Future
                            </h2>
                            @if (!empty($about))
                                <p class="desc wow fadeInUp" data-wow-delay=".8s">{{ $about }}</p>
                            @endif
                        </div>

                        {{-- CTA --}}
                        <div class="btn-area wow fadeInUp" data-wow-delay=".8s">
                            <a class="tj-primary-btn" href="{{ $closingUrl }}">
                                <span class="btn-text"><span>{{ $ctaText }}</span></span>
                                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-6">
                    <div class="about-img-area h6-about-img wow fadeInLeft" data-wow-delay=".2s">
                        <div class="about-img overflow-hidden wow fadeInRight" data-wow-delay=".8s">
                            <img data-speed=".8" src="{{ $aboutImage }}" alt="About Banner">
                        </div>

                        {{-- Circle avatars from course->testimonials --}}
                        @php
                            $avatars = collect(optional($course)->testimonials ?? [])
                                ->map(fn($t) => $t->image
                                    ? (Str::startsWith($t->image, ['http://','https://','/']) ? $t->image : asset('storage/'.$t->image))
                                    : null)
                                ->filter()
                                ->values();
                            $visible = $avatars->take(3);
                            $extraCount = max($avatars->count() - $visible->count(), 0);
                        @endphp

                        <div class="box-area h6-about-box">
                            <div class="customers-box wow fadeInUp" data-wow-delay="1s">
                                <div class="customers">
                                    <ul>
                                        @foreach ($visible as $k => $src)
                                            <li class="wow fadeInLeft" data-wow-delay=".{{ 5 + $k }}s">
                                                <img src="{{ $src }}" alt="testimonial avatar {{ $k + 1 }}">
                                            </li>
                                        @endforeach
                                        @if ($extraCount > 0)
                                            <li class="wow fadeInLeft" data-wow-delay=".8s"><span><i class="tji-plus"></i></span></li>
                                        @endif
                                    </ul>
                                </div>
                                <h5 class="customers-text wow fadeInUp" data-wow-delay=".5s">
                                    We have {{ max(optional($course)->testimonials?->count() ?? 0, 0) }}+ happy trainees.
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-shape-1"><img src="{{ asset('frontend/assets/images/shape/pattern-2.svg') }}" alt=""></div>
        <div class="bg-shape-2"><img src="{{ asset('frontend/assets/images/shape/pattern-3.svg') }}" alt=""></div>
        <div class="bg-shape-3"><img src="{{ asset('frontend/assets/images/shape/shape-blur.svg') }}" alt=""></div>
    </section>

    {{-- ========== Working Process / Eligibility / Why / Dates / CTA ========== --}}
    <section class="h6-working-process section-gap slidebar-stickiy-container">
        <div class="container">
            <div class="row">
                {{-- LEFT: How to Apply --}}
                <div class="col-lg-6 order-2 order-lg-1">
                    <div class="h6-working-process-inner">
                        <div class="h6-working-process-wrapper">
                            @forelse ($howToApply as $i => $label)
                                <div class="process-item h6-working-process-item tj-hover-active-item {{ $i === 0 ? 'active' : '' }}">
                                    <div class="process-step">
                                        <span>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}.</span>
                                    </div>
                                    <div class="process-content">
                                        <h4 class="title">{{ $label }}</h4>
                                    </div>
                                </div>
                            @empty
                                <div class="process-item h6-working-process-item active">
                                    <div class="process-step"><span>01.</span></div>
                                    <div class="process-content">
                                        <h4 class="title">Application instructions coming soon</h4>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Who / Why / Dates / CTA --}}
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="content-wrap slidebar-stickiy">
                        <div class="sec-heading style-2 style-6">
                            <span class="sub-title wow fadeInUp" data-wow-delay=".3s"><i class="tji-box"></i>Who Can Apply</span>
                            <h2 class="sec-title title-anim">Eligibility & Fit</h2>
                        </div>

                        <ul class="desc mb-4 ps-3">
                            @forelse ($whoCanApply as $item)
                                <li class="mb-2">{{ $item }}</li>
                            @empty
                                <li class="mb-2">Eligibility details coming soon.</li>
                            @endforelse
                        </ul>

                        <div class="mt-3">
                            <h3 class="h5 fw-bold mb-2">{{ $whyHeading }}</h3>
                            <p class="desc mb-3">{{ $whyText }}</p>
                        </div>

                        <div class="d-flex flex-column gap-3 mt-4">
                            @if ($startLabel || $endLabel)
                                <div class="p-3 rounded-3 border bg-light">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <span class="fw-semibold">Application Window:</span>
                                            <span class="ms-1">{{ $startLabel ?: 'TBA' }} — {{ $endLabel ?: 'TBA' }}</span>
                                        </div>
                                        {{-- Optional badges for live state
                                        @php
                                            $daysLeft = null;
                                            if ($endDate) {
                                                $end = Carbon::parse($endDate)->endOfDay();
                                                if ($end->isFuture()) $daysLeft = now()->diffInDays($end) + 1;
                                            }
                                        @endphp
                                        @if (!is_null($daysLeft))
                                            <span class="badge text-bg-success rounded-pill">{{ $daysLeft }} day{{ $daysLeft === 1 ? '' : 's' }} left</span>
                                        @elseif($endDate && Carbon::parse($endDate)->isPast())
                                            <span class="badge text-bg-secondary rounded-pill">Closed</span>
                                        @endif
                                        --}}
                                    </div>
                                </div>
                            @endif

                            @if (!empty($important_note))
                                <div class="alert alert-warning rounded-3 mb-0" role="alert">
                                    <strong>Important:</strong> {{ $important_note }}
                                </div>
                            @endif

                            <div class="wow fadeInUp" data-wow-delay=".6s">
                                <div class="mb-2 fw-semibold">{{ $closing_headline }}</div>
                                <a class="tj-primary-btn" href="{{ $closingUrl }}">
                                    <span class="btn-text"><span>{{ $closing_cta_text }}</span></span>
                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ========== Testimonial Section (left text + right slider) ========== --}}
    <section class="h6-testimonial section-gap section-gap-x slidebar-stickiy-container">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="content-wrap slidebar-stickiy">
                        <div class="sec-heading style-2 style-6">
                            <span class="sub-title wow fadeInUp" data-wow-delay=".3s"><i class="tji-box"></i>CLIENT FEEDBACK</span>
                            <h2 class="sec-title title-anim">Our Students Share Their Success Stories</h2>
                        </div>
                        <p class="desc">Hear directly from learners who took this path and unlocked new opportunities.</p>
                        <div class="d-none d-lg-inline-flex wow fadeInUp" data-wow-delay=".6s">
                            <a class="tj-primary-btn" href="{{ $closingUrl }}">
                                <span class="btn-text"><span>{{ $ctaText }}</span></span>
                                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Right column: Course Testimonials Slider --}}
                <div class="col-lg-6">
                    <div class="testimonial-wrapper h6-testimonial-wrapper wow fadeInUp" data-wow-delay=".5s">
                        <div class="swiper swiper-container h6-testimonial-slider">
                            <div class="swiper-wrapper">
                                @forelse($courseTestimonials as $t)
                                    <div class="swiper-slide">
                                        <div class="testimonial-item">
                                            <div class="h6-testimonial-author-wrapper">
                                                <div class="testimonial-author">
                                                    <div class="author-inner">
                                                        <div class="author-img">
                                                            <img src="{{ $avatar($t->image ?? null) }}" alt="{{ $t->name }} avatar">
                                                        </div>
                                                        <div class="author-header">
                                                            <h4 class="title">{{ $t->name }}</h4>
                                                            @if (!empty($t->organization))
                                                                <span class="designation">{{ $t->organization }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="star-ratings">
                                                    <div class="fill-ratings" style="width:100%"><span>★★★★★</span></div>
                                                    <div class="empty-ratings"><span>★★★★★</span></div>
                                                </div>
                                            </div>
                                            <div class="desc">
                                                <p>“{{ Str::of(strip_tags($t->body ?? ''))->trim() }}”</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="swiper-slide">
                                        <div class="testimonial-item">
                                            <div class="h6-testimonial-author-wrapper">
                                                <div class="testimonial-author">
                                                    <div class="author-inner">
                                                        <div class="author-img">
                                                            <img src="{{ asset('frontend/assets/images/avatar-placeholder.png') }}" alt="">
                                                        </div>
                                                        <div class="author-header">
                                                            <h4 class="title">No testimonials yet</h4>
                                                            <span class="designation">Be the first to share!</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="star-ratings">
                                                    <div class="fill-ratings" style="width:0%"><span>★★★★★</span></div>
                                                    <div class="empty-ratings"><span>★★★★★</span></div>
                                                </div>
                                            </div>
                                            <div class="desc">
                                                <p>“Once we receive testimonials for this course, they will appear here.”</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-shape-1"><img src="{{ asset('frontend/assets/images/shape/pattern-2.svg') }}" alt=""></div>
        <div class="bg-shape-2"><img src="{{ asset('frontend/assets/images/shape/pattern-3.svg') }}" alt=""></div>
    </section>

    {{-- ========== Blog (CTAs point to closing URL) ========== --}}
    <section class="tj-blog-section h6-blog section-gap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-heading style-2 style-6 text-center">
                        <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
                            <i class="tji-box"></i>Insights & Ideas
                        </span>
                        <h2 class="sec-title title-anim">The Ultimate Resource.</h2>
                    </div>
                </div>
            </div>

            <div class="row row-gap-4 h6-blog-wrapper">
                @foreach ([1,2,3] as $i)
                    <div class="col-xl-4 col-md-6">
                        <div class="blog-item wow fadeInUp" data-wow-delay=".4s">
                            <div class="blog-thumb">
                                <a href="{{ $closingUrl }}">
                                    <img src="{{ asset("frontend/assets/images/blog/blog-$i.webp") }}" alt="">
                                </a>
                                <div class="blog-date"><span class="date">28</span><span class="month">Feb</span></div>
                            </div>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <span class="categories"><a href="{{ $closingUrl }}">Resource</a></span>
                                    <span>By <a href="{{ $closingUrl }}">Team</a></span>
                                </div>
                                <h4 class="title"><a href="{{ $closingUrl }}">Helpful insight {{ $i }}</a></h4>
                                <a class="text-btn" href="{{ $closingUrl }}">
                                    <span class="btn-text"><span>Read More</span></span>
                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>
</main>



            {{-- Footer (left as-is) --}}
            <footer class="tj-footer-section footer-2 h5-footer  h6-footer   section-gap-x">
                <div class="footer-main-area">
                    <div class="container">
                        <div class="row justify-content-between">
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="footer-widget widget-subscribe h6-footer-subscribe wow fadeInUp"
                                    data-wow-delay=".3s">
                                    <h3 class="title text-anim">Subscribe to Our Newsletter.</h3>
                                    <div class="subscribe-form">
                                        <form action="#">
                                            <input type="email" name="email" placeholder="Enter email">
                                            <button type="submit"><i class="tji-plane"></i></button>
                                            <label for="agree"><input id="agree" type="checkbox">Agree to our
                                                <a href="#">Terms &
                                                    Condition?</a></label>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="footer-widget widget-nav-menu wow fadeInUp" data-wow-delay=".3s">
                                    <h5 class="title">Services</h5>

                                    @php
                                        use App\Models\Service;

                                        // tiny query; adjust filters/columns as needed
                                        $footerServices = Service::query()
                                            ->when(
                                                Schema::hasColumn('services', 'status'),
                                                fn($q) => $q->where('status', 'published'),
                                            )
                                            ->orderByRaw(
                                                Schema::hasColumn('services', 'order') ? '"order" asc' : 'title asc',
                                            )
                                            ->limit(6)
                                            ->get(['title', 'slug']);
                                    @endphp

                                    @if ($footerServices->isNotEmpty())
                                        <ul>
                                            @foreach ($footerServices as $svc)
                                                <li>
                                                    <a
                                                        href="{{ route('services.show', $svc->slug) }}">{{ $svc->title }}</a>
                                                </li>
                                            @endforeach
                                        </ul>

                                        <a class="text-btn mt-2 d-inline-flex align-items-center"
                                            href="{{ route('services') }}">
                                            <span class="btn-text"><span>View all services</span></span>
                                            <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                        </a>
                                    @else
                                        <ul>
                                            <li><a href="{{ route('services') }}">Explore our services</a></li>
                                        </ul>
                                    @endif
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-4 col-md-6">
                                <div class="footer-widget widget-nav-menu wow fadeInUp" data-wow-delay=".5s">
                                    <h5 class="title">Resources</h5>
                                    <ul>
                                        <li><a href="{{ route('about') }}">About Us</a></li>
                                        <li><a href="{{ route('academy') }}">Academy</a></li>
                                        <li><a href="{{ route('services') }}">Services</a></li>
                                        <li><a href="{{ route('events.index') }}">Events</a></li>
                                        <li><a href="{{ route('shop') }}">Shop</a></li>
                                        <li><a href="{{ route('gallery') }}">Gallery</a></li>
                                        <li><a href="{{ route('blog') }}">Blog</a></li>
                                        <li><a href="{{ route('contact') }}">Contact</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3  col-md-6">
                                <div class="footer-widget widget-contact h6-footer-contact wow fadeInUp"
                                    data-wow-delay=".7s">
                                    <h5 class="title">Our Office</h5>
                                    <div class="footer-contact-info">
                                        <div class="contact-item">
                                            <span>Iwaya Road, 58 Iwaya Rd, Yaba, Lagos State.</span>
                                        </div>
                                        <div class="contact-item">
                                            <a href="tel:10095447818">P: +234 703 995 5591
                                            </a></a>
                                            <a href="mailto:support@bexon.com">M:info@forwardedgeconsulting.com</a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h6-footer-logo-area ">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="h6-footer-logo">
                                    <a href="index.html" class="wow fadeInUpBig" data-wow-delay=".3s">
                                        <img src="{{ asset('frontend/assets/images/logos/logo-large.webp') }}"
                                            alt="">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tj-copyright-area-2 h5-footer-copyright h6-footer-copyright">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="copyright-content-area">
                                    <div class="copyright-text">
                                        <p>&copy; 2025 <a href="https://themeforest.net/user/theme-junction/portfolio"
                                                target="_blank">Bexon</a>
                                            All right reserved</p>
                                    </div>
                                    <div class="social-links style-3 style-6">
                                        <ul>
                                            <li><a href="https://facebook.com/forwardedgeconsulting/

"
                                                    target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                                            </li>
                                            <li><a href="https://www.instagram.com/forwardedge_consultingltd
"
                                                    target="_blank"><i class="fa-brands fa-instagram"></i></a>
                                            </li>
                                            <li><a href="https://x.com/ForwardEdgeNg" target="_blank"><i
                                                        class="fa-brands fa-x-twitter"></i></a></li>
                                            <li><a href="https://www.linkedin.com/company/forward-edge-consulting-ltd/
"
                                                    target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="copyright-menu">
                                        <ul>
                                            <li><a href="contact.html">Privacy Policy</a></li>
                                            <li><a href="contact.html">Terms & Condition</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-shape-1">
                    <img src="assets/images/shape/pattern-2.svg" alt="">
                </div>
                <div class="bg-shape-2">
                    <img src="assets/images/shape/pattern-3.svg" alt="">
                </div>
                <div class="bg-shape-3 wow fadeInUpBig" data-wow-delay="1s">
                    <img src="assets/images/shape/footer-bg-shape-blur.svg" alt="">
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('frontend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/gsap.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/ScrollSmoother.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/gsap-scroll-to-plugin.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/gsap-scroll-trigger.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/gsap-split-text.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/jquery-knob.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/swiper.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/odometer.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/venobox.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/appear.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/meanmenu.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/main.js') }}"></script>


</body>

</html>
