@php
  /** @var \App\Models\Block $block */
  $data  = $block->data ?? [];
  $title = $data['title'] ?? null;
  $linkText = $data['link_text'] ?? null;
  $link = $data['link'] ?? null;

  // Accept either "brands" or "logos" as array items.
  // Each item may contain: image (storage path or full URL), alt (optional), href (optional)
  $logos = $data['brands'] ?? $data['logos'] ?? [];

  // Helper to resolve storage vs absolute URL
  $src = function ($path) {
      if (!is_string($path) || trim($path) === '') return null;
      if (Str::startsWith($path, ['http://', 'https://', '//'])) return $path;
      return asset('storage/' . ltrim($path, '/'));
  };
@endphp

<section class="tj-about-section-2 section-gap section-gap-x pb-rich-text" aria-label="Our Partners">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="about-content-area style-3 wow fadeInLeft" data-wow-delay=".3s">
          <div class="sec-heading style-4">
            @if(!blank($data['kicker'] ?? null))
              <div class="subtitle-text">
                <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
                  <i class="tji-box"></i>{!! pb_text($data['kicker']) !!}
                </span>
              </div>
            @endif

            @if(!blank($title))
              <h2 class="sec-title title-highlight">
                {!! pb_text($title) !!}
              </h2>
            @endif
          </div>

          <div class="about-bottom-area">
            @if(!blank($linkText) && !blank($link))
              <div class="about-btn-area-2 wow fadeInUp" data-wow-delay="1s">
                <a class="tj-primary-btn" href="{{ $link }}">
                  <span class="btn-text"><span>{!! pb_text($linkText) !!}</span></span>
                  <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Brand Logos --}}
  @if(!empty($logos))
    <div class="container-fluid client-container-2 gap-top">
      <div class="row">
        <div class="col-12">
          <div class="swiper client-slider client-slider-3">
            <div class="swiper-wrapper">
              @foreach($logos as $i => $logo)
                @php
                  $img = $src($logo['image'] ?? null);
                  $alt = $logo['alt'] ?? ('Brand Logo ' . ($i+1));
                  $href = $logo['href'] ?? null;
                @endphp
                @if($img)
                  <div class="swiper-slide client-item">
                    <div class="client-logo">
                      @if($href)
                        <a href="{{ $href }}" target="_blank" rel="noopener">
                          <img src="{{ $img }}" alt="{{ $alt }}">
                        </a>
                      @else
                        <img src="{{ $img }}" alt="{{ $alt }}">
                      @endif
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
          </div>
          {{-- If Swiper is not present, basic grid fallback --}}
          <noscript>
            <div class="row g-3 mt-3">
              @foreach($logos as $i => $logo)
                @php $img = $src($logo['image'] ?? null); @endphp
                @if($img)
                  <div class="col-6 col-md-3 col-lg-2">
                    <img class="img-fluid" src="{{ $img }}" alt="{{ $logo['alt'] ?? 'Brand Logo' }}">
                  </div>
                @endif
              @endforeach
            </div>
          </noscript>
        </div>
      </div>
    </div>
  @endif

  {{-- Optional background shapes (keep or remove) --}}
  <div class="bg-shape-1">
            <img src="{{asset('frontend/assets/images/shape/pattern-2.svg')}}" alt="">
          </div>
          <div class="bg-shape-2">
            <img src="{{asset('frontend/assets/images/shape/pattern-3.svg')}}" alt="">
          </div>
</section>
