@php
  $d   = $block->data ?? [];
  $ctas = $d['ctas'] ?? [];
@endphp

<section class="tj-cta-section h7-cta section-gap-x pb-rich-text">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="cta-area h7-cta-inner text-center">
          <div class="cta-content">
            @if(!blank($d['title'] ?? null))
              <h2 class="title text-anim mb-2 pb-rich-text">
                {!! pb_text($d['title'] ?? null) !!}
              </h2>
            @endif

            @if (!empty($d['subtitle']))
              <p class="mb-4" style="color: #fff;">
                {!! pb_text($d['subtitle'] ?? null) !!}
              </p>
            @endif

            <div class="d-inline-flex flex-wrap gap-2 justify-content-center">
              @foreach ($ctas as $c)
                @php
                  $href = $c['link']      ?? null;
                  $text = $c['link_text'] ?? null;
                @endphp
                @continue(blank($href) || blank($text))
                <a class="tj-primary-btn" href="{{ $href }}">
                  <span class="btn-text" >{!! pb_text($text) !!}</span>
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
