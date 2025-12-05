@php
  use Illuminate\Support\Str;

  /**
   * Expected $block->data shape:
   * {
   *   "kicker": "OUR PROGRAM",
   *   "title": "Program Includes:",
   *   "subtitle": "What you get",
   *   "desc": "Short overview paragraph...",
   *   "list": ["Live classes", "Hands-on labs", "Career support"],    // (optional) top-level bullets
   *   "link": "/apply",
   *   "link_text": "Learn more",
   *   "items": [
   *     {
   *       "title": "Foundations (5 Weeks)",
   *       "description": "15 live classes, mentor support, certificate.",
   *       "list": ["Live cohort", "Projects", "Certificate"],          // item-level bullets
   *       "image": "programs/foundations.webp",
   *       "link": "/programs/foundations"
   *     },
   *     ...
   *   ]
   * }
   */

  /** @var \App\Models\Block $block */
  $d         = $block->data ?? [];
  $kicker    = $d['kicker']   ?? ($Kicker ?? null);
  $title     = $d['title']    ?? ($title ?? null);
  $subtitle  = $d['subtitle'] ?? null;
  $desc      = $d['desc']     ?? null;
  $topList   = is_array($d['list'] ?? null) ? $d['list'] : [];
  $link      = $d['link']     ?? null;
  $linkText  = $d['link_text']?? null;
  $items     = is_array($d['items'] ?? null) ? $d['items'] : [];
  $itemsCount  = count($items);
  $columnClass = match (true) {
      $itemsCount <= 1 => 'col-12',
      $itemsCount === 2 => 'col-lg-6 col-md-6',
      default => 'col-lg-4 col-md-6',
  };

  // Helpers
  $src = function ($path) {
      if (!$path || !is_string($path)) return null;
      if (Str::startsWith($path, ['http://','https://','//'])) return $path;
      // allow theme assets to pass through unchanged
      if (Str::startsWith($path, ['frontend/','assets/'])) return asset($path);
      return asset('storage/' . ltrim($path, '/'));
  };

  $resolveProgramImage = function ($path, $i = 0) use ($src) {
      return $src($path);
  };

  // Normalize list: accept ["text", ...] or [{"text":"..."}]
  $asLines = function($maybeList) {
      if (!is_array($maybeList)) return [];
      return array_values(array_filter(array_map(function($item){
          if (is_string($item)) return trim($item);
          if (is_array($item) && isset($item['text'])) return trim((string) $item['text']);
          return null;
      }, $maybeList)));
  };
@endphp

<section class="h6-service section-gap pb-rich-text" aria-label="Program Includes">
  <div class="container">
    {{-- Header --}}
    <div class="row">
      <div class="col-12">
        <div class="sec-heading sec-heading-centered style-2 style-6">
          @if(!empty($kicker))
            <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
              <i class="tji-box"></i>{!! pb_text($kicker) !!}
            </span>
          @endif

          @if(!blank($title))
            <h2 class="sec-title title-anim">{!! pb_text($title) !!}</h2>
          @endif

          @if(!empty($subtitle))
            <div class="mt-2 lead">{!! pb_text($subtitle) !!}</div>
          @endif

          @if(!empty($desc))
            <p class="desc wow fadeInUp mt-2" data-wow-delay=".45s">{!! pb_text($desc) !!}</p>
          @endif

          @if(count($asLines($topList)))
            <ul class="list-unstyled d-inline-block text-start mt-3">
              @foreach($asLines($topList) as $li)
                <li class="d-flex align-items-start gap-2 mb-1">
                  <i class="tji-check-circle mt-1"></i>
                  <span>{!! pb_text($li) !!}</span>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>
    </div>

    {{-- Slider --}}
    <div class="row">
      <div class="col-12">
        <div class="h6-service-slider swiper">
          <div class="swiper-wrapper">
            @forelse ($items as $i => $item)
              @php
                $itTitle = $item['title'] ?? '';
                $itDesc  = $item['description'] ?? ($item['desc'] ?? null);
                $itList  = $asLines($item['list'] ?? []);
                $img     = $resolveProgramImage($item['image'] ?? null, $i);
                $idx     = str_pad($i + 1, 2, '0', STR_PAD_LEFT) . '.';
                $itLink  = $item['link'] ?? $link ?? null;
              @endphp

              <div class="swiper-slide {{ $columnClass }}">
                <div class="h6-service-item">
                  @if($img)
                    <div class="h6-service-thumb">
                      @if($itLink)
                        <a href="{{ $itLink }}"><img src="{{ $img }}" alt="{{ $itTitle ?: 'Program feature' }}" style></a>
                      @else
                        <img src="{{ $img }}" alt="{{ $itTitle ?: 'Program feature' }}" style>
                      @endif
                    </div>
                  @endif

                  <div class="h6-service-content">
                    <h5 class="h6-service-index">{{ $idx }}</h5>

                    <div class="h6-service-title-wrap">
                      @if(!blank($itTitle))
                        <h4 class="title">
                        @if($itLink)
                          <a href="{{ $itLink }}">{!! pb_text($itTitle) !!}</a>
                        @else
                          {!! pb_text($itTitle) !!}
                        @endif
                      </h4>
                      @endif

                      {{-- ===== Item description ===== --}}
                      @if(!empty($itDesc))
                        <p class="mt-1 mb-2">{!! pb_text($itDesc) !!}</p>
                      @endif

                      {{-- ===== Item list ===== --}}
                      @if(count($itList))
                        <ul class="list-unstyled m-0 mb-2">
                          @foreach($itList as $li)
                            <li class="d-flex align-items-start gap-2 mb-1">
                              <i class="tji-check-circle mt-1"></i>
                              <span>{!! pb_text($li) !!}</span>
                            </li>
                          @endforeach
                        </ul>
                      @endif

                      @if(!blank($itLink))
                        <a class="text-btn" href="{{ $itLink }}">
                          <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                        </a>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @empty
              {{-- Intentionally left blank when there are no items --}}
            @endforelse
          </div>

          <div class="swiper-pagination-area"></div>
        </div>
      </div>
    </div>
  </div>
</section>
