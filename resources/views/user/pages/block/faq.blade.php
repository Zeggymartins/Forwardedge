@php
  $d = $block->data ?? [];
  $items = is_array($d['items'] ?? null) ? $d['items'] : [];

  // unique id for the accordion (avoid collision if multiple FAQ blocks exist)
  $accId = 'faq-' . ($block->id ?? uniqid());
@endphp

<!-- start: Faq Section -->
<section class="h7-faq section-gap slidebar-stickiy-container pb-rich-text">
  <div class="container">
    <div class="row justify-content-between">
      <!-- Left: sticky heading -->
      <div class="col-12 col-lg-4">
        <div class="sec-heading style-2 style-7 slidebar-stickiy">
          @if(!blank($d['kicker'] ?? null))
            <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
              <i class="tji-box"></i> {!! pb_text($d['kicker'] ?? null) !!}
            </span>
          @endif
          @if(!blank($d['title'] ?? null))
            <h2 class="sec-title text-anim">{!! pb_text($d['title'] ?? null) !!}</h2>
          @endif

          @php
            $phoneRaw = $d['phone_raw'] ?? null;
            $phoneDisplay = $d['phone_display'] ?? $phoneRaw;
          @endphp
          @if(!blank($phoneRaw) || !blank($phoneDisplay))
            <a class="number" href="{{ $phoneRaw ? 'tel:' . $phoneRaw : '#' }}">
              <span class="call-icon"><i class="tji-phone"></i></span>
              <span>{!! pb_text($phoneDisplay) !!}</span>
            </a>
          @endif
        </div>
      </div>

      <!-- Right: accordion -->
      <div class="col-12 col-lg-8">
        <div class="accordion tj-faq style-2 h7-faq-wrapper" id="{{ $accId }}">
          @foreach ($items as $i => $it)
            @php
              $itemId  = $accId . '-' . ($i + 1);
              $isOpen  = $i === 0; // first item open
              $q = $it['q'] ?? '';
              $a = $it['a'] ?? '';
            @endphp
            @continue(blank($q) && blank($a))

            <div class="accordion-item {{ $isOpen ? 'active' : '' }} wow fadeInUp" data-wow-delay=".3s">
              <button class="faq-title {{ $isOpen ? '' : 'collapsed' }}"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#{{ $itemId }}"
                      aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                {!! pb_text($q) !!}
              </button>

              <div id="{{ $itemId }}" class="collapse {{ $isOpen ? 'show' : '' }}" data-bs-parent="#{{ $accId }}">
                <div class="accordion-body faq-text">
                  <p>{!! pb_text($a) !!}</p>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>
</section>
<!-- end: Faq Section -->
