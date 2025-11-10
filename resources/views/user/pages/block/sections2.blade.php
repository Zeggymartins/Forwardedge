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
  $kicker        = $d['kicker']        ?? 'OUR FUN FACT';
  $title         = $d['title']         ?? 'Numbers and facts that define performance.';
  $progressText  = $d['desc'] ?? null;
  $items         = is_array($d['items'] ?? null) ? $d['items'] : [];
  $itemsCount    = count($items);
  $columnClass   = $itemsCount <= 2 ? 'col-lg-6 col-md-6' : 'col-lg-4 col-md-6';

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

@push('styles')
<style>
  .tj-funfact-section .heading-wrap-content{
    display:flex;
    flex-wrap:wrap;
    gap:1.5rem;
    align-items:flex-end;
    justify-content:space-between;
  }

  .tj-funfact-section .progress-item{
    padding:.9rem 1.5rem;
    border-radius:999px;
    background:rgba(15,23,42,.06);
    border:1px solid rgba(15,23,42,.08);
    min-width:240px;
  }

  .tj-funfact-section .progress-item .sub-title{
    margin:0;
    color:#0f172a;
    font-weight:600;
    letter-spacing:.08em;
    text-transform:uppercase;
  }
</style>
@endpush

<!-- start: Fun fact Section (cards behave like "sections") -->
<section class="tj-funfact-section section-gap section-gap-x" aria-label="Fun facts">
  <div class="container">
    {{-- Heading --}}
    <div class="row">
      <div class="col-lg-12">
        <div class="heading-wrap-content">
          <div class="sec-heading style-4">
            @if(!empty($kicker))
              <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
                <i class="tji-box"></i>{{ $kicker }}
              </span>
            @endif
            <h2 class="sec-title title-anim">{{ $title }}</h2>
          </div>

          @if(!empty($progressText))
            <div class="progress-item">
              <div class="progress-text">
                <span class="sub-title">{{ $progressText }}</span>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Cards --}}
    <div class="row row-gap-4">
      @forelse($items as $i => $item)
        @php
          $icon  = $item['icon'] ?? 'tji-complete';
          $head  = $item['title'] ?? '';
          $sub   = $item['subtitle'] ?? null;      // replaces the inline number area
          $desc  = $item['description'] ?? null;
          $list  = $asLines($item['list'] ?? []);
          $idx   = str_pad($i + 1, 2, '0', STR_PAD_LEFT) . '.';
        @endphp
        <div class="{{ $columnClass }}">
          <div class="countup-item style-2 wow fadeInUp" data-wow-delay=".{{ [7,5,1][$i % 3] }}s">
            <span class="count-icon"><i class="{{ $icon }}"></i></span>
            <span class="steps">{{ $idx }}</span>

            <div class="count-inner">
              {{-- Title (like "count-text") --}}
              @if($head)
                <span class="count-text d-block">{{ $head }}</span>
              @endif

              {{-- Replaced block: subtitle + description + list --}}
              @if($sub)
                <div class="mt-2 fw-medium">{{ $sub }}</div>
              @endif

              @if($desc)
                <p class="mb-2 mt-1">{{ $desc }}</p>
              @endif

              @if(count($list))
                <ul class="list-unstyled m-0">
                  @foreach($list as $li)
                    <li class="d-flex align-items-start gap-2 mb-1">
                      <i class="tji-check-circle mt-1"></i>
                      <span>{{ $li }}</span>
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
        <div class="{{ $columnClass }}">
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
