@php
  /**
   * Expected $block->data shape:
   * {
   *   "kicker": "OUR FUN FACT",
   *   "title": "Numbers and facts that define performance.",
   *   "progress_text": "Increased revenue in the last 6 months.",   // optional small line under heading
   *   "items": [
   *     {
   *       "icon": "tji-complete",                                    // optional icon class
   *       "title": "Faster Growth",
   *       "subtitle": "Outcome-focused delivery",                    // <— replaces inline numbers
   *       "description": "We help teams ship faster with measurable KPIs.",
   *       "list": ["Data-driven roadmaps", "SLAs & uptime", "Trusted partners"]
   *     },
   *     {
   *       "icon": "tji-complete",
   *       "title": "Reach Worldwide",
   *       "subtitle": "Active in 25+ regions",
   *       "description": "Localized delivery & support.",
   *       "list": ["Regional partners", "24/7 coverage", "Compliance-ready"]
   *     },
   *     ...
   *   ]
   * }
   */

  /** @var \App\Models\Block $block */
  $d = $block->data ?? [];
  $kicker        = $d['kicker']        ?? null;
  $title         = $d['title']         ?? null;
  $progressText  = $d[''] ?? null;
  $items         = is_array($d['items'] ?? null) ? $d['items'] : [];

  // Normalize list entries: accept ["text", ...] or [{"text":"..."}]
  $asLines = function($maybeList) {
      if (!is_array($maybeList)) return [];
      return array_values(array_filter(array_map(function($item){
          if (is_string($item)) return trim($item);
          if (is_array($item) && isset($item['text'])) return trim((string)$item['text']);
          return null;
      }, $maybeList)));
  };
@endphp

<!-- start: Fun fact Section (cards behave like "sections") -->
<section class="tj-funfact-section section-gap section-gap-x pb-rich-text" aria-label="Fun facts">
  <div class="container">
    {{-- Heading --}}
    <div class="row">
      <div class="col-lg-12">
        <div class="heading-wrap-content">
          <div class="sec-heading">
            @if(!empty($kicker))
              <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
                <i class="tji-box"></i>{!! pb_text($kicker) !!}
              </span>
            @endif
            <h2 class="sec-title title-anim">{!! pb_text($title) !!}</h2>
          </div>

          @if(!empty($progressText))
            <p class="desc wow fadeInUp" data-wow-delay=".5s">{!! pb_text($progressText) !!}
          @endif
        </div>
      </div>
    </div>

    {{-- Cards --}}
    <div class="row row-gap-4">
      @forelse($items as $i => $item)
        @php
          $icon  = $item['icon'] ?? null;
          $head  = $item['title'] ?? '';
          $sub   = $item['subtitle'] ?? null;      // replaces the inline number area
          $desc  = $item['description'] ?? null;
          $list  = $asLines($item['list'] ?? []);
          $idx   = str_pad($i + 1, 2, '0', STR_PAD_LEFT) . '.';
        @endphp
        <div class="col-lg-4 col-md-6">
          <div class="countup-item style-2 wow fadeInUp" data-wow-delay=".{{ [7,5,1][$i % 3] }}s">
            @if(!blank($icon))
              <span class="count-icon"><i class="{{ $icon }}"></i></span>
            @endif
            <span class="steps">{{ $idx }}</span>

            <div class="count-inner">
              {{-- Title (like "count-text") --}}
              @if($head)
                <span class="count-text d-block">{!! pb_text($head) !!}</span>
              @endif

              {{-- Replaced block: subtitle + description + list --}}
              @if($sub)
                <div class="mt-2 fw-medium">{!! pb_text($sub) !!}</div>
              @endif

              @if($desc)
                <p class="mb-2 mt-1">{!! pb_text($desc) !!}</p>
              @endif

              @if(count($list))
                <ul class="list-unstyled m-0">
                  @foreach($list as $li)
                    @continue(blank($li))
                    <li class="d-flex align-items-start gap-2 mb-1">
                      <i class="tji-check-circle mt-1"></i>
                      <span>{!! pb_text($li) !!}</span>
                    </li>
                  @endforeach
                </ul>
              @endif
              {{-- /Replaced block --}}
            </div>
          </div>
        </div>
      @empty
        {{-- Optional: render 1 placeholder card when empty --}}
        <div class="col-lg-4 col-md-6">
          <div class="countup-item style-2">
            <span class="count-icon"><i class="tji-complete"></i></span>
            <span class="steps">01.</span>
            <div class="count-inner">
              <span class="count-text">Your Headline</span>
              <div class="mt-2 fw-medium">Your subtitle here</div>
              <p class="mb-2 mt-1">Add a short descriptive line about this highlight.</p>
              <ul class="list-unstyled m-0">
                <li class="d-flex align-items-start gap-2 mb-1">
                  <i class="tji-check-circle mt-1"></i><span>First bullet</span>
                </li>
                <li class="d-flex align-items-start gap-2 mb-1">
                  <i class="tji-check-circle mt-1"></i><span>Second bullet</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      @endforelse
    </div>
  </div>
  <div class="bg-shape-1">
    <img src="assets/images/shape/pattern-2.svg" alt="">
  </div>
  <div class="bg-shape-2">
    <img src="assets/images/shape/pattern-3.svg" alt="">
  </div>
</section>

        <!-- end: Fun fact Section -->
