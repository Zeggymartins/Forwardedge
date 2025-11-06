@php
  /** @var \App\Models\Block $block */
  $d = $block->data ?? [];

  // Data-driven fields (from block->data)
  $heroTitle = $d['title']      ?? ($heroTitle ?? 'Kickstart your journey');
  $linkText  = $d['link_text']  ?? ($linkText  ?? 'Get Started');
  $link      = $d['link']       ?? ($link      ?? '#');
  $desc      = $d['desc']       ?? ($Desc      ?? null);

  // Image can be absolute URL or storage path in data['hero_image'] / data['banner_image']
  $imgRaw    = $d['hero_image'] ?? $d['banner_image'] ?? ($heroImage ?? null);
  $src = function ($path) {
      if (!$path || !is_string($path)) return null;
      if (Str::startsWith($path, ['http://','https://','//'])) return $path;
      return asset('storage/' . ltrim($path, '/'));
  };
  $heroImage = $src($imgRaw) ?? asset('frontend/assets/images/banner/hero-fallback.webp');

  /**
   * YEAR (NOT from data):
   * Expecting $year to be injected by a view composer/helper later.
   * If none provided yet, keep null (no year rendered).
   *
   * Example future sources (we’ll add later): fe_current_year(), config('site.campaign_year'), etc.
   */
  $year = $year
      ?? (function_exists('fe_current_year') ? fe_current_year() : null)
      ?? null;
@endphp

<section class="tj-banner-section h6-hero section-gap-x" aria-label="Hero">
  <div class="banner-area">
    <div class="banner-left-box">
      <div class="banner-content">
        <h1 class="banner-title title-anim">{{ $heroTitle }}</h1>

        <div class="btn-area wow fadeInUp" data-wow-delay=".8s">
          <a class="tj-primary-btn" href="{{ $link }}">
            <span class="btn-text"><span>{{ $linkText }}</span></span>
            <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
          </a>
        </div>

        <div class="h6-hero-bottom wow fadeInLeft mt-2" data-wow-delay=".9s">
          <div class="h6-hero-history">
            @if (!empty($year))
              <h4 class="h6-hero-history-title mt-3">{{ $year }}</h4>
            @endif
            @if (!empty($desc))
              <p class="h6-hero-history-desc">{{ $desc }}</p>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="banner-right-box">
      <div class="banner-img wow fadeInUp" data-wow-delay=".3s">
        <img data-speed=".8" src="{{ $heroImage }}" alt="Hero Banner">
      </div>
    </div>
  </div>
</section>
