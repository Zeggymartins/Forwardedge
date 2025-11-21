@php
    $d = $block->data ?? [];

    // Left heading/copy defaults
    $kicker = $d['kicker'] ?? null;
    $title = $d['title'] ?? null;
    $subtitle = $d['subtitle'] ?? null; // optional short lead
    $text = $d['text'] ?? null; // longer paragraph
    $list = is_array($d['list'] ?? null) ? $d['list'] : []; // bullet list (array)
    $cta = $d['cta'] ?? null; // ['text'=>..., 'link'=>...]

    // Cards (no icons) — 2 per row
    $cards = is_array($d['cards'] ?? null) ? $d['cards'] : [];

    // Right side visuals
    $bannerLeft = $d['banner_left'] ?? null;

    // Tiles (4-up grid on the right)
    $tiles = is_array($d['tiles'] ?? null) ? $d['tiles'] : [];
@endphp
@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    // Helper to normalize any image path to a web URL (define once)
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

    // Prefer explicit variables, else fall back to the $d[...] payload
    $kicker     = $kicker     ?? ($d['kicker']     ?? null);
    $title      = $title      ?? ($d['title']      ?? null);
    $subtitle   = $subtitle   ?? ($d['subtitle']   ?? null);
    $text       = $text       ?? ($d['text']       ?? null);
    $list       = $list       ?? ($d['list']       ?? []);
    $cards      = $cards      ?? ($d['cards']      ?? []);
    $cta        = $cta        ?? ($d['cta']        ?? []);
    $tiles      = $tiles      ?? ($d['tiles']      ?? []); // optional

    // Banner (left) - accept either controller vars or $d['banner_left']
    $bannerLeftPath = $bannerLeft ?? ($banner_left ?? ($d['banner_left'] ?? null));
    $bannerLeftUrl  = $imgUrl($bannerLeftPath);
@endphp

@push('styles')
<style>
  /* Card surface */
  .h7-about-card {
    background: #fff;
    border: 1px solid var(--tj-border, #edf2f7);
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,.06);
    padding: 1.25rem;
    height: 100%;
    display: flex;
    flex-direction: column;
  }
  .h7-about-card .desc {
    white-space: normal;
    overflow-wrap: anywhere;   /* modern */
    word-wrap: break-word;     /* legacy */
    word-break: break-word;    /* WebKit fallback */
    hyphens: auto;             /* nicer breaks for real words */
  }
  /* Big responsive circle for card image */
  .circle-wrap {
    aspect-ratio: 1 / 1;
    width: clamp(180px, 65%, 320px);
    border-radius: 9999px;
    overflow: hidden;
    margin: 0 auto 0.5rem auto;
    border: 6px solid var(--tj-gold, #FDB714);
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
  }
  .circle-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  /* Text rhythm inside cards */
  .h7-about-card .title { margin: .25rem 0 .5rem; font-weight: 600; }
  .h7-about-card .desc { margin: 0; color: var(--tj-text-muted, #6c757d); }

  /* Extra space between columns so cards don't "enter" each other */
  .about-cards-row { row-gap: 1.5rem; } /* vertical gap */
</style>
@endpush

<section class="tj-about-section h7-about section-gap section-gap-x mt-10 pb-rich-text">
  <div class="container">
    <div class="row row-gap-4">
      <div class="col-12">
        <div class="about-content-area-2 wow fadeInUp" data-wow-delay=".3s">
          <div class="sec-heading style-2 style-7">
            <div class="row">
              <!-- Kicker -->
              <div class="col-12 col-lg-4">
                @if(!empty($kicker))
                  <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
                    <i class="tji-box"></i>{!! pb_text($kicker) !!}
                  </span>
                @endif
              </div>

              <!-- Title, subtitle, text, list, cards, CTA -->
              <div class="col-12 col-lg-8">
                <div class="h7-about-content-inner pb-rich-text">
                  @if(!empty($title))
                    <h2 class="sec-title title-highlight">{!! pb_text($title) !!}</h2>
                  @endif

                  @if(!empty($subtitle))
                    <p class="mb-2 text-muted">{!! pb_text($subtitle) !!}</p>
                  @endif

                  @if(!empty($text))
                    <p class="mb-3">{!! pb_text($text) !!}</p>
                  @endif

                  @if(!empty($list))
                    <ul class="mb-4">
                      @foreach($list as $li)
                        @continue(blank($li))
                        <li>{!! pb_text($li) !!}</li>
                      @endforeach
                    </ul>
                  @endif

                  {{-- Cards (image + title + text) --}}
                  @if (!empty($cards))
                    <div class="row g-4 about-cards-row mb-4">
                      @foreach ($cards as $c)
                        <div class="col-12 col-md-6">
                          <div class="h7-about-card">
                            @if (!empty($c['image']))
                              <div class="mb-3 d-flex justify-content-center">
                                <div class="circle-wrap">
                                  <img
                                    src="{{ $imgUrl($c['image'] ?? null) }}"
                                    alt="{{ $c['title'] ?? 'About card' }}">
                                </div>
                              </div>
                            @endif

                            @if (!empty($c['title']))
                              <h4 class="title">{!! pb_text($c['title']) !!}</h4>
                            @endif

                            @if (!empty($c['text']))
                              <p class="desc">{!! pb_text($c['text']) !!}</p>
                            @endif
                          </div>
                        </div>
                      @endforeach
                    </div>
                  @endif

                  {{-- CTA (link_text + link) --}}
                  @if(!empty($cta['link_text']) && !empty($cta['link']))
                    <div class="about-btn-area-2 wow fadeInUp" data-wow-delay="1s">
                      <a class="tj-primary-btn" href="{{ $cta['link'] }}">
                        <span class="btn-text"><span>{!! pb_text($cta['link_text']) !!}</span></span>
                        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                      </a>
                    </div>
                  @endif
                </div> <!-- /.h7-about-content-inner -->
              </div>
            </div> <!-- /.row -->
          </div> <!-- /.sec-heading -->
        </div>
      </div>
    </div>
  </div>

  {{-- Bottom visuals row --}}
  <div class="container-fluid gap-15">
    <div class="row">
      {{-- Left big banner --}}
      <div class="col-12 col-xl-6">
        <div class="h7-about-banner wow fadeInUp" data-wow-delay=".2s">
          <img data-speed=".8" src="{{ $bannerLeftUrl }}" alt="About banner">
        </div>
      </div>

      {{-- Right 4-up tiles (optional) --}}
      <div class="col-12 col-xl-6">
        <div class="row h7-about-counter-wrapper">
          @foreach(($tiles ?? []) as $i => $t)
            @php $delay = .3 + ($i * .1); @endphp

            @if(($t['type'] ?? '') === 'counter')
              <div class="col-12 col-md-6">
                <div class="countup-item style-2 wow fadeInUp" data-wow-delay="{{ $delay }}s">
                  @if(!blank($t['label'] ?? null))
                    <p class="counter-title">{!! pb_text($t['label']) !!}</p>
                  @endif
                  <div class="count-inner">
                    @if(!blank($t['value'] ?? null))
                      <div class="inline-content">
                        <span class="odometer countup-number" data-count="{{ $t['value'] }}"></span>
                        @if(!empty($t['suffix']))<span class="count-plus">{!! pb_text($t['suffix']) !!}</span>@endif
                      </div>
                    @endif
                    @if(!empty($t['note']))<span class="count-text">{!! pb_text($t['note']) !!}</span>@endif
                  </div>
                </div>
              </div>

            @elseif(($t['type'] ?? '') === 'image')
              <div class="col-12 col-md-6">
                <div class="img-box style-2 wow fadeInUp"
                     data-wow-delay="{{ $delay }}s"
                     data-bg-image="{{ $imgUrl($t['bg'] ?? null) }}">
                </div>
              </div>

            @elseif(($t['type'] ?? '') === 'customers')
              <div class="col-12 col-md-6">
                <div class="customers-box style-2 wow fadeInUp" data-wow-delay="{{ $delay }}s">
                  <div class="customers-bg"
                       data-bg-image="{{ $imgUrl($t['bg'] ?? null) }}">
                  </div>
                  @if(!empty($t['text']))
                    <h6 class="customers-text wow fadeInLeft" data-wow-delay=".6s">{!! pb_text($t['text']) !!}</h6>
                  @endif
                  @if(!empty($t['link']) && !empty($t['link_text']))
                    <a class="text-btn wow fadeInLeft" data-wow-delay=".5s" href="{{ $t['link'] }}">
                      <span class="btn-text"><span>{!! pb_text($t['link_text']) !!}</span></span>
                      <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                    </a>
                  @endif
                </div>
              </div>
            @endif
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- Decorative shapes --}}
  <div class="bg-shape-1">
    <img src="{{ asset('frontend/assets/images/shape/pattern-2.svg') }}" alt="">
  </div>
  <div class="bg-shape-2">
    <img src="{{ asset('frontend/assets/images/shape/pattern-3.svg') }}" alt="">
  </div>
</section>


<!-- end: About Section -->
