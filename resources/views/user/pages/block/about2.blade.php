@php
  /**
   * Expected $block->data shape:
   * {
   *   "kicker": "Get to Know Us",
   *   "title": "We deliver measurable results",
   *   "text": "Short paragraph...",
   *   "text_color": "#ffffff",
   *   "link_text": "Learn More",
   *   "link": "https://example.com/about",
   *   "about_image": "blocks/about/banner.webp",
   *   "columns": [
   *     {
   *       "head": "Why Choose Us",
   *       "subhead": "Outcome-focused",
   *       "description": "We align work to results.",
   *       "list": ["Data-driven roadmaps", "SLAs & uptime", "Trusted partners"]
   *     },
   *     {
   *       "head": "What You Get",
   *       "subhead": "Full-stack support",
   *       "description": "From strategy to delivery.",
   *       "list": ["Discovery & research", "Implementation", "Enablement & training"]
   *     }
   *   ]
   * }
   */

  $data = $block->data ?? [];

  $kicker    = $data['kicker']    ?? ($Kicker    ?? null);
  $title     = $data['title']     ?? ($title     ?? null);
  $text      = $data['text']      ?? ($text      ?? null);
  $linkText  = $data['link_text'] ?? ($link_Text ?? null);
  $link      = $data['link']      ?? ($link      ?? null);
  $textColor = $data['text_color'] ?? '#ffffff';

  $columns  = $data['columns'] ?? [];
  $colA = $columns[0] ?? [];
  $colB = $columns[1] ?? [];

  $aboutImage = $data['about_image'] ?? ($aboutImage ?? null);

  $src = function($path) {
      if (!$path || !is_string($path)) return null;
      if (Str::startsWith($path, ['http://','https://','//'])) return $path;
      return asset('storage/' . ltrim($path, '/'));
  };

  $asLines = function($maybeList) {
      if (!is_array($maybeList)) return [];
      return array_values(array_filter(array_map(function($item){
          if (is_string($item)) return trim($item);
          if (is_array($item) && isset($item['text'])) return trim((string)$item['text']);
          return null;
      }, $maybeList)));
  };

  // Column accent colors (alternating)
  $colAccents = ['#FDB714', '#0d6efd'];
@endphp

@push('styles')
<style>
  /* Scoped to this block instance */
  .h6-about-{{ $block->id }} .sec-title,
  .h6-about-{{ $block->id }} .sub-title,
  .h6-about-{{ $block->id }} .about-content-area,
  .h6-about-{{ $block->id }} .about-content-area *:not(.a2-col-card) {
    color: {{ $textColor }} !important;
  }
  .h6-about-{{ $block->id }} .about-content-area .text-muted,
  .h6-about-{{ $block->id }} .about-content-area .small {
    color: {{ $textColor }}cc !important;
  }

  /* Column cards */
  .a2-col-card {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 16px;
    padding: 1.5rem 1.25rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    transition: transform .2s, box-shadow .2s;
  }
  .a2-col-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(0,0,0,.18);
  }
  .a2-col-accent-bar {
    width: 40px;
    height: 4px;
    border-radius: 4px;
    flex-shrink: 0;
  }
  .a2-col-card .a2-col-head {
    font-size: 1.15rem;
    font-weight: 700;
    margin: 0;
    color: #fff;
  }
  .a2-col-card .a2-col-subhead {
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: .04em;
    text-transform: uppercase;
    opacity: .72;
    color: #fff;
    margin: 0;
  }
  .a2-col-card .a2-col-desc {
    font-size: 0.9rem;
    opacity: .88;
    color: #fff;
    margin: 0;
    line-height: 1.6;
  }
  .a2-col-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  .a2-col-list li {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    font-size: 0.88rem;
    color: #fff;
    opacity: .9;
  }
  .a2-col-list li .a2-check {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
    font-size: 10px;
    color: #fff;
  }
</style>
@endpush

{{-- ========== About 2 ========== --}}
<section class="tj-about-section h6-about h6-about-{{ $block->id }} section-gap section-gap-x pb-rich-text" aria-label="About">
  <div class="container">
    <div class="row gy-5 align-items-center">

      {{-- Left: text + columns --}}
      <div class="col-xl-6 col-lg-6">
        <div class="about-content-area h6-about-content style-1 wow fadeInLeft" data-wow-delay=".2s">
          <div class="sec-heading style-2 style-6">

            @if(!empty($kicker))
              <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
                <i class="tji-box"></i>{!! pb_text($kicker) !!}
              </span>
            @endif

            <h2 class="sec-title title-anim">
              {!! pb_text($title) !!}
            </h2>

            @if (!empty($text))
              <p class="desc wow fadeInUp" data-wow-delay=".8s">{!! pb_text($text) !!}</p>
            @endif

            {{-- Two-column cards --}}
            @php
              $activeCols = array_filter([$colA, $colB], fn($c) => !empty($c['head']) || !empty($c['subhead']) || !empty($c['description']) || !empty($c['list']));
            @endphp
            @if(count($activeCols))
              <div class="row g-3 mt-3">
                @foreach ([$colA, $colB] as $ci => $col)
                  @php
                    $head  = $col['head']        ?? null;
                    $sub   = $col['subhead']      ?? null;
                    $desc  = $col['description']  ?? null;
                    $items = $asLines($col['list'] ?? []);
                    $accent = $colAccents[$ci] ?? '#FDB714';
                  @endphp
                  @if($head || $sub || $desc || count($items))
                    <div class="col-md-6 wow fadeInUp" data-wow-delay="{{ 0.3 + ($ci * 0.15) }}s">
                      <div class="a2-col-card">
                        <div class="a2-col-accent-bar" style="background:{{ $accent }}"></div>

                        @if($head)
                          <h4 class="a2-col-head">{!! pb_text($head) !!}</h4>
                        @endif

                        @if($sub)
                          <p class="a2-col-subhead">{!! pb_text($sub) !!}</p>
                        @endif

                        @if($desc)
                          <p class="a2-col-desc">{!! pb_text($desc) !!}</p>
                        @endif

                        @if(count($items))
                          <ul class="a2-col-list">
                            @foreach($items as $li)
                              @continue(blank($li))
                              <li>
                                <span class="a2-check" style="background:{{ $accent }}">
                                  <i class="tji-check" style="font-size:9px"></i>
                                </span>
                                <span>{!! pb_text($li) !!}</span>
                              </li>
                            @endforeach
                          </ul>
                        @endif
                      </div>
                    </div>
                  @endif
                @endforeach
              </div>
            @endif
            {{-- /Two-column cards --}}

          </div>

          {{-- CTA --}}
          @if($linkText && $link)
            <div class="btn-area wow fadeInUp mt-4" data-wow-delay=".8s">
              <a class="tj-primary-btn" href="{{ $link }}">
                <span class="btn-text"><span>{!! pb_text($linkText) !!}</span></span>
                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
              </a>
            </div>
          @endif
        </div>
      </div>

      {{-- Right: image --}}
      <div class="col-xl-6 col-lg-6">
        <div class="about-img-area h6-about-img wow fadeInRight" data-wow-delay=".3s">
          <div class="about-img overflow-hidden">
            @if($src($aboutImage))
              <img data-speed=".8" src="{{ $src($aboutImage) }}" alt="About Banner">
            @else
              <img data-speed=".8" src="{{ asset('frontend/assets/images/about/about-1.webp') }}" alt="About Banner">
            @endif
          </div>
        </div>
      </div>

    </div>
  </div>

  {{-- Background shapes --}}
  <div class="bg-shape-1">
    <img src="{{ asset('frontend/assets/images/shape/pattern-2.svg') }}" alt="">
  </div>
  <div class="bg-shape-2">
    <img src="{{ asset('frontend/assets/images/shape/pattern-3.svg') }}" alt="">
  </div>
  <div class="bg-shape-3">
    <img src="{{ asset('frontend/assets/images/shape/shape-blur.svg') }}" alt="">
  </div>
</section>
