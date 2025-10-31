@php
  /**
   * Expected $block->data shape:
   * {
   *   "kicker": "Get to Know Us",
   *   "title": "We deliver measurable results",
   *   "text": "Short paragraph...",
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
   *   ],
   *   
   * }
   */

  $data = $block->data ?? [];

  $kicker   = $data['kicker']   ?? ($Kicker   ?? null);
  $title    = $data['title']    ?? ($title    ?? 'We deliver measurable results, build trust, and grow with you.');
  $text     = $data['text']     ?? ($text     ?? null);
  $linkText = $data['link_text']?? ($link_Text?? null);
  $link     = $data['link']     ?? ($link     ?? '#');

  $columns  = $data['columns'] ?? [];
  // Force exactly two columns (render what exists)
  $colA = $columns[0] ?? [];
  $colB = $columns[1] ?? [];

  $aboutImage = $data['about_image'] ?? ($aboutImage ?? null);

  $src = function($path) {
      if (!$path || !is_string($path)) return null;
      if (Str::startsWith($path, ['http://','https://','//'])) return $path;
      return asset('storage/' . ltrim($path, '/'));
  };

  // Helper to normalize list items: accept ["text", ...] or [{"text":"..."}]
  $asLines = function($maybeList) {
      if (!is_array($maybeList)) return [];
      return array_values(array_filter(array_map(function($item){
          if (is_string($item)) return trim($item);
          if (is_array($item) && isset($item['text'])) return trim((string)$item['text']);
          return null;
      }, $maybeList)));
  };


@endphp

{{-- ========== About ========== --}}
<section class="tj-about-section h6-about section-gap section-gap-x" aria-label="About">
  <div class="container">
    <div class="row">
      {{-- Text / Left --}}
      <div class="col-xl-6 col-lg-6">
        <div class="about-content-area h6-about-content style-1 wow fadeInLeft" data-wow-delay=".2s">
          <div class="sec-heading style-2 style-6">
            @if(!empty($kicker))
              <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
                <i class="tji-box"></i>{{ $kicker }}
              </span>
            @endif

            <h2 class="sec-title title-anim">
              {{ $title }}
            </h2>

            @if (!empty($text))
              <p class="desc wow fadeInUp" data-wow-delay=".8s">{{ $text }}</p>
            @endif

            {{-- ===== Two-column list block (head, subhead, description, list) ===== --}}
            <div class="row g-4 mt-2">
              @foreach ([$colA, $colB] as $col)
                @php
                  $head = $col['head'] ?? null;
                  $sub  = $col['subhead'] ?? null;
                  $desc = $col['description'] ?? null;
                  $list = $asLines($col['list'] ?? []);
                @endphp
                @if($head || $sub || $desc || count($list))
                  <div class="col-md-6">
                    <div class="pe-md-3">
                      @if($head)
                        <h4 class="mb-1">{{ $head }}</h4>
                      @endif
                      @if($sub)
                        <div class="text-muted small mb-2">{{ $sub }}</div>
                      @endif
                      @if($desc)
                        <p class="mb-3">{{ $desc }}</p>
                      @endif
                      @if(count($list))
                        <ul class="list-unstyled m-0">
                          @foreach($list as $li)
                            <li class="d-flex align-items-start gap-2 mb-2">
                              <i class="tji-check-circle mt-1"></i>
                              <span>{{ $li }}</span>
                            </li>
                          @endforeach
                        </ul>
                      @endif
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
            {{-- ===== /Two-column list block ===== --}}
          </div>

          {{-- CTA --}}
          @if($linkText && $link)
            <div class="btn-area wow fadeInUp mt-3" data-wow-delay=".8s">
              <a class="tj-primary-btn" href="{{ $link }}">
                <span class="btn-text"><span>{{ $linkText }}</span></span>
                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
              </a>
            </div>
          @endif
        </div>
      </div>

      {{-- Image / Right --}}
      <div class="col-xl-6 col-lg-6">
        <div class="about-img-area h6-about-img wow fadeInLeft" data-wow-delay=".2s">
          <div class="about-img overflow-hidden wow fadeInRight" data-wow-delay=".8s">
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

  {{-- Background shapes (overridable via data) --}}
  <div class="bg-shape-1">
    <img src="{{  asset('frontend/assets/images/shape/pattern-2.svg') }}" alt="">
  </div>
  <div class="bg-shape-2">
    <img src="{{  asset('frontend/assets/images/shape/pattern-3.svg') }}" alt="">
  </div>
  <div class="bg-shape-3">
    <img src="{{  asset('frontend/assets/images/shape/shape-blur.svg') }}" alt="">
  </div>
</section>
