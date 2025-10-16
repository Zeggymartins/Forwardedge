@extends('user.master_page')

@section('main')
<main id="primary" class="site-main">
  <div class="top-space-15"></div>

  {{-- =========================================================
       HERO: if $scholarship exists, show one-slide hero with
       background image + text overlay (uses SAME theme classes)
  ========================================================== --}}
  @if(!empty($scholarship))
    <section class="tj-slider-section">
      <div class="swiper hero-slider">
        <div class="swiper-wrapper">
          <div class="swiper-slide tj-slider-item">
            <div class="slider-bg-image"
                 @if($scholarship->hero_image)
                   data-bg-image="{{ asset('storage/'.$scholarship->hero_image) }}"
                 @else
                   data-bg-image="{{ asset('frontend/assets/images/hero/slider-1.webp') }}"
                 @endif>
            </div>

            <div class="container">
              <div class="slider-wrapper">
                <div class="slider-content">
                  <h1 class="slider-title">
                    {{ $scholarship->hero_headline ?? 'Scholarship Opportunity' }}
                  </h1>

                  @if(!empty($scholarship->hero_subtext))
                    <div class="slider-desc">{!! nl2br(e($scholarship->hero_subtext)) !!}</div>
                  @endif

                  <div class="slider-btn d-flex align-items-center gap-3 flex-wrap">
                    @if($scholarship->cta_text && $scholarship->cta_url)
                      <a class="tj-primary-btn" href="{{ $scholarship->cta_url }}">
                        <span class="btn-text"><span>{{ $scholarship->cta_text }}</span></span>
                        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                      </a>
                    @endif

                    @if($scholarship->opens_at || $scholarship->closes_at)
                      <small class="text-white-50 d-block">
                        @if($scholarship->opens_at) Opens: {{ $scholarship->opens_at->format('M j, Y') }} @endif
                        @if($scholarship->opens_at && $scholarship->closes_at) • @endif
                        @if($scholarship->closes_at) Closes: {{ $scholarship->closes_at->format('M j, Y') }} @endif
                      </small>
                    @endif
                  </div>
                </div>
              </div>
            </div>

          </div> {{-- /swiper-slide --}}
        </div>
        {{-- keep navigation minimal, works with theme JS --}}
        <div class="hero-navigation d-inline-flex">
          <div class="slider-prev">
            <span class="anim-icon"><i class="tji-arrow-left"></i><i class="tji-arrow-left"></i></span>
          </div>
          <div class="slider-next">
            <span class="anim-icon"><i class="tji-arrow-right"></i><i class="tji-arrow-right"></i></span>
          </div>
        </div>
      </div>
    </section>
  @else
    {{-- Fallback: your original multi-slide hero (left intact) --}}
    <section class="tj-slider-section">
      <div class="swiper hero-slider">
        <div class="swiper-wrapper">
          {{-- Slide 1 --}}
          <div class="swiper-slide tj-slider-item">
            <div class="slider-bg-image" data-bg-image="assets/images/hero/slider-1.webp"></div>
            <div class="container">
              <div class="slider-wrapper">
                <div class="slider-content">
                  <h1 class="slider-title">Leading Future for <span>Business.</span></h1>
                  <div class="slider-desc">Committed to delivering innovative solutions that drive success. With a focus on quality.</div>
                  <div class="slider-btn">
                    <a class="tj-primary-btn" href="contact.html">
                      <span class="btn-text"><span>Get Started</span></span>
                      <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {{-- Slide 2 --}}
          <div class="swiper-slide tj-slider-item">
            <div class="slider-bg-image" data-bg-image="assets/images/hero/slider-2.webp"></div>
            <div class="container">
              <div class="slider-wrapper">
                <div class="slider-content">
                  <h1 class="slider-title">Leading Future for <span>Business.</span></h1>
                  <div class="slider-desc">Committed to delivering innovative solutions that drive success. With a focus on quality.</div>
                  <div class="slider-btn">
                    <a class="tj-primary-btn" href="contact.html">
                      <span class="btn-text"><span>Get Started</span></span>
                      <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {{-- Slide 3 --}}
          <div class="swiper-slide tj-slider-item">
            <div class="slider-bg-image" data-bg-image="assets/images/hero/slider-3.webp"></div>
            <div class="container">
              <div class="slider-wrapper">
                <div class="slider-content">
                  <h1 class="slider-title">Leading Future for <span>Business.</span></h1>
                  <div class="slider-desc">Committed to delivering innovative solutions that drive success. With a focus on quality.</div>
                  <div class="slider-btn">
                    <a class="tj-primary-btn" href="contact.html">
                      <span class="btn-text"><span>Get Started</span></span>
                      <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div> {{-- /swiper-wrapper --}}
        <div class="hero-navigation d-inline-flex">
          <div class="slider-prev"><span class="anim-icon"><i class="tji-arrow-left"></i><i class="tji-arrow-left"></i></span></div>
          <div class="slider-next"><span class="anim-icon"><i class="tji-arrow-right"></i><i class="tji-arrow-right"></i></span></div>
        </div>
      </div>
      <div class="swiper hero-thumb">
        <div class="swiper-wrapper">
          <div class="swiper-slide thumb-item"><img src="assets/images/hero/slider-thumb-1.webp" alt="Thumbnail"></div>
          <div class="swiper-slide thumb-item"><img src="assets/images/hero/slider-thumb-2.webp" alt="Thumbnail"></div>
          <div class="swiper-slide thumb-item"><img src="assets/images/hero/slider-thumb-3.webp" alt="Thumbnail"></div>
        </div>
      </div>
      <div class="circle-text-wrap">
        <span class="circle-text" data-bg-image="assets/images/hero/circle-text.webp"></span>
        <a class="circle-icon" href="service.html"><i class="tji-arrow-down-big"></i></a>
      </div>
    </section>
  @endif

  {{-- ============================================
       SCHOLARSHIP DETAILS (only when available)
  ============================================= --}}
  @if(!empty($scholarship))
    @php
      $includes = array_values(array_filter(($scholarship->program_includes ?? []), fn($v)=>trim((string)$v) !== ''));
      $who      = array_values(array_filter(($scholarship->who_can_apply ?? []), fn($v)=>trim((string)$v) !== ''));
      $steps    = array_values(array_filter(($scholarship->how_to_apply   ?? []), fn($v)=>trim((string)$v) !== ''));
    @endphp

    <section class="section-gap">
      <div class="container">
        {{-- About --}}
        @if(!empty($scholarship->about))
          <div class="row mb-4">
            <div class="col-lg-10">
              <div class="sec-heading style-2">
                <span class="sub-title">About the Scholarship</span>
                <p class="mt-2">{!! nl2br(e($scholarship->about)) !!}</p>
              </div>
            </div>
          </div>
        @endif

        <div class="row row-gap-4">
          {{-- Program Includes --}}
          @if(count($includes))
            <div class="col-xl-4 col-md-6">
              <div class="pricing-box">
                <div class="pricing-header">
                  <h4 class="package-name">Program Includes</h4>
                </div>
                <div class="list-items">
                  <ul>
                    @foreach($includes as $li)
                      <li><i class="tji-list"></i>{{ $li }}</li>
                    @endforeach
                  </ul>
                </div>
              </div>
            </div>
          @endif

          {{-- Who Can Apply --}}
          @if(count($who))
            <div class="col-xl-4 col-md-6">
              <div class="pricing-box">
                <div class="pricing-header">
                  <h4 class="package-name">Who Can Apply?</h4>
                </div>
                <div class="list-items">
                  <ul>
                    @foreach($who as $li)
                      <li><i class="tji-list"></i>{{ $li }}</li>
                    @endforeach
                  </ul>
                </div>
              </div>
            </div>
          @endif

          {{-- How to Apply --}}
          @if(count($steps))
            <div class="col-xl-4 col-md-6">
              <div class="pricing-box">
                <div class="pricing-header">
                  <h4 class="package-name">How to Apply</h4>
                </div>
                <div class="list-items">
                  <ol class="ps-3 mb-0">
                    @foreach($steps as $li)
                      <li class="mb-1">{{ $li }}</li>
                    @endforeach
                  </ol>
                </div>
              </div>
            </div>
          @endif
        </div>

        {{-- Important Note --}}
        @if(!empty($scholarship->important_note))
          <div class="schedule-card mt-4">
            <p class="mb-0">{!! nl2br(e($scholarship->important_note)) !!}</p>
          </div>
        @endif

        {{-- Closing CTA --}}
        @if($scholarship->closing_headline || ($scholarship->closing_cta_text && $scholarship->closing_cta_url))
          <div class="text-center mt-5">
            @if($scholarship->closing_headline)
              <h3 class="mb-3">{{ $scholarship->closing_headline }}</h3>
            @endif
            @if($scholarship->closing_cta_text && $scholarship->closing_cta_url)
              <a class="tj-primary-btn" href="{{ $scholarship->closing_cta_url }}">
                <span class="btn-text"><span>{{ $scholarship->closing_cta_text }}</span></span>
                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
              </a>
            @endif
          </div>
        @endif
      </div>
    </section>
  @endif
</main>
@endsection
