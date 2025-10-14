@extends('user.master_page')
@section('title', ' Academy | Forward Edge Consulting')
@section('main')
@include('user.partials.breadcrumb')
     <!-- start: Service Section -->
<section class="tj-service-section service-4 section-gap">
    <div class="container">
      <div class="row row-gap-4">
  @forelse ($courses as $index => $course)
    <div class="col-lg-4 col-md-6">
      <div class="service-item style-4 wow fadeInUp" data-wow-delay=".{{ $index + 1 }}s">
        <div class="service-icon">
          {{-- You can map icons manually or store them in DB --}}
          <i class="tji-service-{{ ($index % 6) + 1 }}"></i>
        </div>
        <div class="service-content">
          <h4 class="title">
            <a href="{{ route('course.show', $course->slug) }}">{{ $course->title }}</a>
          </h4>
          <p class="desc">{{ Str::limit($course->description, 140) }}</p>
          <a class="text-btn" href="{{ route('course.show', $course->slug) }}">
            <span class="btn-text"><span>Go to Course</span></span>
            <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
          </a>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12">
      <div class="d-flex flex-column align-items-center text-center py-5" style="gap:20px;">
        {{-- Empty state SVG: Happy person w/ confetti --}}
        <svg width="340" height="220" viewBox="0 0 680 440" fill="none" xmlns="http://www.w3.org/2000/svg" style="max-width: 80%; height: auto;">
          <defs>
            <linearGradient id="gradMain" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0%"  stop-color="var(--tj-gold, #FDB714)"/>
              <stop offset="100%" stop-color="var(--tj-blue, #2c99d4)"/>
            </linearGradient>
            <linearGradient id="gradSoft" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0%"  stop-color="rgba(253,183,20,.25)"/>
              <stop offset="100%" stop-color="rgba(44,153,212,.25)"/>
            </linearGradient>
            <clipPath id="clipRounded">
              <rect x="0" y="0" width="680" height="440" rx="24"/>
            </clipPath>
          </defs>

          <g clip-path="url(#clipRounded)">
            <!-- Soft background blob -->
            <ellipse cx="340" cy="360" rx="300" ry="60" fill="url(#gradSoft)"/>

            <!-- Confetti -->
            <g opacity=".9">
              <circle cx="80"  cy="80"  r="6"  fill="url(#gradMain)"/>
              <circle cx="620" cy="90"  r="5"  fill="url(#gradMain)"/>
              <rect   x="120" y="60"  width="10" height="10" rx="2" fill="url(#gradMain)"/>
              <rect   x="560" y="60"  width="10" height="10" rx="2" fill="url(#gradMain)"/>
              <circle cx="520" cy="140" r="4"  fill="url(#gradMain)"/>
              <circle cx="160" cy="140" r="4"  fill="url(#gradMain)"/>
            </g>

            <!-- Person w/ laptop -->
            <!-- Body -->
            <path d="M290 300 C290 260, 390 260, 390 300 L390 340 L290 340 Z" fill="url(#gradMain)" opacity=".95"/>
            <!-- Head -->
            <circle cx="340" cy="220" r="42" fill="#fff"/>
            <circle cx="340" cy="220" r="40" fill="url(#gradSoft)"/>
            <!-- Smile -->
            <path d="M322 230 C334 245, 346 245, 358 230" stroke="url(#gradMain)" stroke-width="6" stroke-linecap="round" fill="none"/>
            <!-- Eyes -->
            <circle cx="326" cy="214" r="5" fill="url(#gradMain)"/>
            <circle cx="354" cy="214" r="5" fill="url(#gradMain)"/>

            <!-- Laptop -->
            <rect x="280" y="280" width="120" height="70" rx="10" fill="#ffffff" stroke="url(#gradMain)" stroke-width="4"/>
            <circle cx="340" cy="315" r="8" fill="url(#gradMain)"/>

            <!-- Ground line -->
            <rect x="120" y="360" width="440" height="8" rx="4" fill="url(#gradMain)" opacity=".2"/>
          </g>
        </svg>

        <div>
          <h3 class="mb-2" style="font-weight:800; letter-spacing:.2px;">
            Bootcamps are coming soon 🎉
          </h3>
          <p class="mb-3" style="color:#6c757d; max-width:620px;">
            We’re prepping new cohorts right now. Check back shortly—or get notified the moment enrollment opens.
          </p>
          <a href="{{ route('academy') }}"
             class="btn btn-gradient px-4 py-2"
             style="background: linear-gradient(135deg, var(--tj-gold,#FDB714), var(--tj-blue,#2c99d4)); color:#fff; border:none; border-radius:999px;">
            Back to Academy
          </a>
          {{-- Optional notify button (remove if not needed) --}}
          {{-- <a href="{{ route('notify.me') }}" class="btn btn-outline-dark ms-2 px-4 py-2" style="border-radius:999px;">Notify me</a> --}}
        </div>
      </div>
    </div>
  @endforelse
</div>

    </div>
</section>



    
@endsection