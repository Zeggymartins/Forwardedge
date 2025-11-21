@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
  $d = $block->data ?? [];
  $items = is_array($d['items'] ?? null) ? $d['items'] : [];
  if (!isset($imgUrl)) {
        $imgUrl = function ($path = null, $fallbackAsset = null) {
            if (blank($path)) {
                return $fallbackAsset ? asset($fallbackAsset) : '';
            }
            if (Str::startsWith($path, ['http://', 'https://', '//', '/'])) {
                return $path; // already absolute
            }
            return Storage::url($path); // e.g. blocks/... -> /storage/blocks/...
        };
    }
@endphp
@push('styles')
<style>
  /* Swiper slide/container sanity */
  .testimonial-wrapper .swiper-slide { overflow: visible; }

  /* Ensure flex children can actually shrink/wrap */
  .testimonial-wrapper .testimonial-item,
  .testimonial-wrapper .testimonial-item > *,
  .testimonial-wrapper .desc {
    min-width: 0;          /* critical for wrapping inside flex */
    max-width: 100%;
  }

  /* Force wrapping, even for very long unbroken text */
  .testimonial-wrapper .desc p {
    white-space: normal !important;
    overflow-wrap: anywhere;   /* modern */
    word-wrap: break-word;     /* legacy */
    word-break: break-word;    /* webkit fallback */
    hyphens: auto;
    margin: 0;                 /* tighter rhythm */
    line-height: 1.6;          /* readability */
  }

  /* Optional: give the text a little breathing room from header */
  .testimonial-wrapper .author-header + .desc { margin-top: .5rem; }

  /* Optional: if the card uses grid/flex layout, keep text from sticking to edges */
  .testimonial-wrapper .testimonial-item {
    display: flex;
    flex-direction: column;
    gap: .75rem;               /* space between blocks inside the card */
  }
</style>
@endpush

<!-- start: Testimonial Section -->
<section class="tj-testimonial-section h7-testimonial section-gap section-gap-x pb-rich-text">
  <div class="container">
    <div class="row justify-content-between">
      <div class="col-12">
        <div class="sec-heading style-2 style-7 sec-heading-centered">
          @if(!blank($d['kicker'] ?? null))
            <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
              <i class="tji-box"></i> {!! pb_text($d['kicker'] ?? null) !!}
            </span>
          @endif
          @if(!blank($d['title'] ?? null))
            <h2 class="sec-title text-anim">{!! pb_text($d['title'] ?? null) !!}</h2>
          @endif
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="testimonial-wrapper h7-testimonial-wrapper wow fadeInRightBig" data-wow-delay=".3s">
          <div class="swiper swiper-container testimonial-slider">
            <div class="swiper-wrapper">
              @foreach($items as $it)
                @php
                    $name = $it['name'] ?? '';
                    $designation = $it['designation'] ?? '';
                    $text = $it['text'] ?? '';
                @endphp
                @continue(blank($name) && blank($text))
                <div class="swiper-slide">
                  <div class="testimonial-item">
                    <div class="testimonial-author">
                      <div class="author-inner">
                        <div class="author-img">
                          @php $photo = $imgUrl($it['photo'] ?? null); @endphp
                          @if($photo)
                            <img src="{{ $photo }}" alt="{{ $name ?: 'Client photo' }}">
                          @endif
                        </div>
                        <div class="author-header">
                          @if(!blank($name))
                            <h4 class="title">{!! pb_text($name) !!}</h4>
                          @endif
                          @if(!blank($designation))
                            <span class="designation">{!! pb_text($designation) !!}</span>
                          @endif
                        </div>
                      </div>
                    </div>
                    @if(!blank($text))
                        <div class="desc">
                          <p>{!! pb_text($text) !!}</p>
                        </div>
                    @endif
                    
                    <div class="star-ratings">
                      <div class="fill-ratings" style="width: {{ (int)($it['rating_fill'] ?? 100) }}%">
                        <span>★★★★★</span>
                      </div>
                      <div class="empty-ratings">
                        <span>★★★★★</span>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>

            <div class="swiper-pagination-area"></div>
          </div>

          <div class="slider-prev">
            <span class="anim-icon">
              <i class="tji-arrow-left"></i>
              <i class="tji-arrow-left"></i>
            </span>
          </div>
          <div class="slider-next">
            <span class="anim-icon">
              <i class="tji-arrow-right"></i>
              <i class="tji-arrow-right"></i>
            </span>
          </div>

          <div class="bg-shape-3">
            <img src="{{ asset('frontend/assets/images/shape/h7-testimonial-shape-blur.svg') }}" alt="">
          </div>
        </div>
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
