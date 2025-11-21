@php
  $d = $block->data ?? [];

  // Left-side heading defaults
  $kicker          = $d['kicker']          ?? null;
  $section_title   = $d['section_title']   ?? null;
  $more_link       = $d['link']            ?? null;
  $more_link_text  = $d['link_text']       ?? null;

  // Items (image + title + link_text + link)
  $items = is_array($d['items'] ?? null) ? $d['items'] : [];
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

@push('styles')
<style>
  .fe-gallery-section .project-wrapper{
    max-width: 640px;
    margin-left: auto;
  }
  .fe-gallery-section .project-item{
    padding: 1.25rem;
    border-radius: 32px;
    background: #fff;
    border: 1px solid rgba(15,23,42,0.08);
    box-shadow: 0 25px 70px rgba(15,23,42,0.08);
  }
  .fe-gallery-section .project-img{
    border-radius: 28px;
    overflow: hidden;
    margin-bottom: 1.25rem;
  }
  .fe-gallery-section .project-img img{
    width: 100%;
    display: block;
    object-fit: cover;
    min-height: 420px;
  }
  @media (max-width: 991px){
    .fe-gallery-section .project-wrapper{
      max-width: 100%;
      margin-top: 2rem;
    }
    .fe-gallery-section .project-img img{
      min-height: 280px;
    }
  }
</style>
@endpush

<section class="h7-project section-gap tj-sticky-panel-container fe-gallery-section pb-rich-text">
  <div class="container">
    <div class="row">
      {{-- Left: sticky heading & CTA --}}
      <div class="col-12 col-lg-4">
        <div class="sec-heading style-2 style-7 tj-sticky-panel">
          @if(!blank($kicker))
            <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
              <i class="tji-box"></i> {!! pb_text($kicker) !!}
            </span>
          @endif
          @if(!blank($section_title))
            <h2 class="sec-title text-anim">{!! pb_text($section_title) !!}</h2>
          @endif

          @if(!blank($more_link) && !blank($more_link_text))
            <div class="wow fadeInUp" data-wow-delay=".3s">
              <a class="tj-primary-btn" href="{{ $more_link }}">
                <span class="btn-text"><span>{!! pb_text($more_link_text) !!}</span></span>
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
            @php
              $image = $imgUrl($it['image'] ?? null);
            @endphp
            @continue(blank($image) && blank($it['title'] ?? null) && blank($it['link'] ?? null))
            <div class="project-item h4-project-item tj-sticky-panel wow fadeInUp" data-wow-delay=".{{ 3 + $i }}s">
              <div class="project-img">
                @if($image)
                  <img src="{{ $image }}" alt="{{ $it['title'] ?? 'Gallery item' }}">
                @endif
              </div>
              <div class="project-content">
                <div class="project-text">
                  <div>
                    @if(!blank($it['title'] ?? null))
                      <h3 class="title">
                        @if(!blank($it['link'] ?? null))
                          <a href="{{ $it['link'] }}">{!! pb_text($it['title']) !!}</a>
                        @else
                          {!! pb_text($it['title']) !!}
                        @endif
                      </h3>
                    @endif
                  </div>
                  @if(!blank($it['link'] ?? null))
                    <a class="tji-icon-btn" href="{{ $it['link'] }}" aria-label="{{ $it['link_text'] ?? 'Open' }}">
                      <i class="tji-arrow-right-long"></i>
                    </a>
                  @endif
                </div>
                {{-- Optional inline button under title (if you want visible text button too) --}}
                @if(!blank($it['link_text'] ?? null) && !blank($it['link'] ?? null))
                      <div class="mt-2">
                        <a class="text-btn" href="{{ $it['link'] }}">
                          <span class="btn-text"><span>{!! pb_text($it['link_text']) !!}</span></span>
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
