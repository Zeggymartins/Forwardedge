@php
  $d   = $block->data ?? [];
  $ctas = $d['ctas'] ?? [];

  // sensible fallback so the section is never empty
  if (empty($ctas)) {
      $ctas = [
          ['link' => '#', 'link_text' => 'Get Started'],
      ];
  }
@endphp

<section class="tj-cta-section h7-cta section-gap-x">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="cta-area h7-cta-inner text-center">
          <div class="cta-content">
            <h2 class="title text-anim mb-2">
              {{ $d['title'] ?? 'Ready to begin?' }}
            </h2>

            @if (!empty($d['subtitle']))
              <p class="mb-4" style="color: #fff;">
                {{ $d['subtitle'] }}
              </p>
            @endif

            <div class="d-inline-flex flex-wrap gap-2 justify-content-center">
              @foreach ($ctas as $c)
                @php
                  $href = $c['link']      ?? '#';
                  $text = $c['link_text'] ?? 'Learn more';
                @endphp
                <a class="tj-primary-btn" href="{{ $href }}">
                  <span class="btn-text" >{{ $text }}</span>
                  <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                </a>
              @endforeach
            </div>
          </div> <!-- /.cta-content -->
        </div>   <!-- /.cta-area -->
      </div>
    </div>
  </div>
</section>
