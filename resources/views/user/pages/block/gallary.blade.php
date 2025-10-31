@php
  $d = $block->data ?? [];

  // Left-side heading defaults
  $kicker          = $d['kicker']          ?? 'Proud Projects';
  $section_title   = $d['section_title']   ?? 'Driving Success Through Our Projects.';
  $more_link       = $d['link']       ?? null;
  $more_link_text  = $d['link_text']  ?? 'Explore More';

  // Items (image + title + link_text + link)
  $items = $d['items'] ?? [
      [
          'image'     => 'assets/images/project/project-4.webp',
          'title'     => 'Event Management Platform',
          'link_text' => 'View Project',
          'link'      => '#',
      ],
      [
          'image'     => 'assets/images/project/project-8.webp',
          'title'     => 'Rebranding Strategy for a Growing Brand',
          'link_text' => 'View Project',
          'link'      => '#',
      ],
      [
          'image'     => 'assets/images/project/project-9.webp',
          'title'     => 'Customer Support Automation',
          'link_text' => 'View Project',
          'link'      => '#',
      ],
  ];
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
@endphp
<section class="h7-project section-gap tj-sticky-panel-container">
  <div class="container">
    <div class="row">
      {{-- Left: sticky heading & CTA --}}
      <div class="col-12 col-lg-4">
        <div class="sec-heading style-2 style-7 tj-sticky-panel">
          <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
            <i class="tji-box"></i> {{ $kicker }}
          </span>
          <h2 class="sec-title text-anim">{{ $section_title }}</h2>

          @if(!empty($more_link))
            <div class="wow fadeInUp" data-wow-delay=".3s">
              <a class="tj-primary-btn" href="{{ $more_link }}">
                <span class="btn-text"><span>{{ $more_link_text }}</span></span>
                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
              </a>
            </div>
          @endif
        </div>
      </div>

      {{-- Right: gallery/projects list --}}
      <div class="col-12 col-lg-8">
        <div class="project-wrapper h7-project-wrapper">
          @foreach($items as $i => $it)
            <div class="project-item h4-project-item tj-sticky-panel wow fadeInUp" data-wow-delay=".{{ 3 + $i }}s">
              <div class="project-img">
                <img src="{{ $imgUrl($it['image'] ?? 'assets/images/project/project-4.webp') }}" alt="Image">
              </div>
              <div class="project-content">
                <div class="project-text">
                  <div>
                    <h3 class="title">
                      <a href="{{ $it['link'] ?? '#' }}">{{ $it['title'] ?? '' }}</a>
                    </h3>
                  </div>
                  <a class="tji-icon-btn" href="{{ $it['link'] ?? '#' }}" aria-label="{{ $it['link_text'] ?? 'Open' }}">
                    <i class="tji-arrow-right-long"></i>
                  </a>
                </div>
                {{-- Optional inline button under title (if you want visible text button too) --}}
                @if(!empty($it['link_text']))
                  <div class="mt-2">
                    <a class="text-btn" href="{{ $it['link'] ?? '#' }}">
                      <span class="btn-text"><span>{{ $it['link_text'] }}</span></span>
                      <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                    </a>
                  </div>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>
</section>
