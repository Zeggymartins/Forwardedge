@php
  $d = $block->data ?? [];
  $items = $d['items'] ?? [];
  // sensible defaults if editor hasn’t added items yet
  if (empty($items)) {
      $items = [
          ['subtitle' => 'Foundations (Live, 5 Weeks)', 'text' => '15 live classes, hands-on labs, certificate'],
          ['subtitle' => 'Specializations (Self-Paced)', 'text' => 'Pentesting, SOC, or GRC — choose later'],
          ['subtitle' => 'Tools & Projects', 'text' => 'Real tools across 8+ lab envs & 200+ exercises'],
      ];
  }
@endphp

<section id="choose" class="tj-choose-section h6-choose h7-choose section-gap">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="sec-heading style-2 style-7 text-center">
          <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
            <i class="tji-box"></i> {{ $d['kicker'] ?? 'Overview' }}
          </span>
          <h2 class="sec-title text-anim">{{ $d['title'] ?? 'Program Overview' }}</h2>
        </div>
      </div>
    </div>

    <div class="row rightSwipeWrap h7-choose-item-wrapper wow fadeInLeftBig" data-wow-delay=".4s">
      @foreach ($items as $i => $it)
        @php
          $delay = 5 + $i; // .5s, .6s, .7s...
          $icon  = $it['icon'] ?? 'tji-innovative';
          $href  = $it['link'] ?? ($d['link'] ?? '#');
          $label = $it['link_text'] ?? ($d['link_text'] ?? 'Get Started');
        @endphp
        <div class="col-lg-4 h7-choose-item">
          <div class="choose-box h6-choose-box h7-choose-box wow fadeInUp" data-wow-delay=".{{ $delay }}s">
            <div class="choose-content">
              <div class="choose-icon">
                <i class="{{ $icon }}"></i>
              </div>
              <h4 class="title">{{ $it['subtitle'] ?? '' }}</h4>
              <p class="desc">{{ $it['text'] ?? '' }}</p>

              <a class="text-btn" href="{{ $href }}">
                <span class="btn-text"><span>{{ $label }}</span></span>
                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
              </a>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
