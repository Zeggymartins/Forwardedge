@php
  /** @var \App\Models\Block $block */
  $d = $block->data ?? [];

  // Slides: each = ['title' => string, 'image' => 'path/to/file']
  // Your admin form posts files into slides[*][image] and stores path back into $d['slides'][*]['image'].
  $slides = $d['slides'] ?? [
    ['title' => 'Growth',     'image' => 'frontend/assets/images/marquee/marquee-1.webp'],
    ['title' => 'Leadership', 'image' => 'frontend/assets/images/marquee/marquee-2.webp'],
  ];

  // Make a unique class to scope the Swiper instance (so multiple blocks can coexist)
  $uid = 'marquee-' . ($block->id ?? uniqid());
@endphp


@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!isset($imgUrl)) {
  $imgUrl = function ($path = null, $fallbackAsset = null) {
      if (blank($path)) return $fallbackAsset ? asset($fallbackAsset) : '';
      if (Str::startsWith($path, ['http://','https://','//','/'])) return $path;
      return Storage::url($path); // "blocks/..." -> "/storage/blocks/..."
  };
}

// Accept slides from $slides or $d['slides']
$slides = $slides ?? ($d['slides'] ?? []);
@endphp
@if(!empty($slides))
<section class="tj-marquee-section section-gap-x {{ $uid ?? '' }}">
  <div class="marquee-wrapper">
    <div class="swiper marquee-slider">
      <div class="swiper-wrapper">
        @foreach($slides as $s)
          @php
            $title = data_get($s, 'title', '');
            $img   = $imgUrl(data_get($s, 'image'), 'frontend/assets/images/marquee/marquee-1.webp');
          @endphp

          <div class="swiper-slide marquee-item">
            @if($title !== '')
              <h4 class="marquee-text mb-0">{{ $title }}</h4>
            @endif
            <div class="marquee-img">
              <img src="{{ $img }}" alt="{{ $title ?: 'Slide' }}" loading="lazy">
            </div>
          </div>
        @endforeach
      </div>

      {{-- Optional pagination --}}
      <div class="swiper-pagination"></div>
    </div>
  </div>
</section>





@endif
