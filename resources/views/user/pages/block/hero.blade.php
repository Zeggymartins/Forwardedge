@php $d = $block->data ?? []; @endphp

<section class="h4-banner-section section-gap-x">
  <div class="h4-banner-area">
    <div class="h4-banner-content">
      <span class="sub-title wow fadeInUp" data-wow-delay=".2s">
        <i class="tji-box"></i>
        {{ $d['slug'] ?? 'bootcamp' }}
      </span>

      <h1 class="banner-title text-anim">{{ $d['title'] ?? 'Bootcamp' }}</h1>

      <div class="banner-desc-area wow fadeInUp" data-wow-delay=".7s">
        <a class="tj-primary-btn" href="{{ $d['link'] ?? '#' }}">
          <span class="btn-text"><span>{{ $d['link_text'] ?? 'Get Started' }}</span></span>
          <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
        </a>

        <div class="banner-desc">
          {{ $d['sub_text'] ?? 'Committed to delivering innovative solutions that drive success. With a focus on quality.' }}
        </div>
      </div>
    </div>

    <div class="banner-img-area">
 @php
    $img = $d['banner_image'] ?? null;
    $src = $img
        ? (\Illuminate\Support\Str::startsWith($img, ['http://','https://','//','/'])
            ? $img
            : \Illuminate\Support\Facades\Storage::url($img))
        : asset('frontend/assets/images/hero/h4-hero-img.webp');
@endphp

<div class="banner-img">
  <img data-speed="0.8" src="{{ $src }}" alt="{{ $d['title'] ?? 'Banner' }}">
</div>


      <div class="h4-rating-box wow fadeInUp" data-wow-delay="1s">
        <h2 class="title">4.8</h2>
        <p class="desc">Global rating based on 100+ reviews</p>
      </div>
    </div>
  </div>

  <div class="bg-shape-1">
    <img src="{{ asset('frontend/assets/images/shape/pattern-2.svg') }}" alt="">
  </div>
  <div class="bg-shape-2">
    <img src="{{ asset('frontend/assets/images/shape/pattern-3.svg') }}" alt="">
  </div>
</section>
