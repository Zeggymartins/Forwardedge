@php
  $d = $block->data ?? [];
  $items = $d['items'] ?? [];

  // sensible defaults (renders even with no data)
  if (empty($items)) {
      $items = [
          [
              'q' => 'What services does Bexon offer to clients?',
              'a' => 'We provide strategy, CX, and sustainability solutions tailored to your goals. Start by contacting us for a short consultation.'
          ],
          [
              'q' => 'How do I get started with Corporate Business?',
              'a' => 'Reach out via the contact form or call us. We’ll align on scope, timelines, and success metrics.'
          ],
          [
              'q' => 'How do you ensure the success of a project?',
              'a' => 'Clear milestones, frequent check-ins, and QA reviews at every phase to ensure on-time, high-quality delivery.'
          ],
          [
              'q' => 'How long will it take to complete my project?',
              'a' => 'Depends on scope; most engagements run 4–12 weeks. We’ll share a detailed timeline after discovery.'
          ],
          [
              'q' => 'Can I track the progress of my project?',
              'a' => 'Yes — shared dashboards and weekly updates keep you fully in the loop.'
          ],
      ];
  }

  // unique id for the accordion (avoid collision if multiple FAQ blocks exist)
  $accId = 'faq-' . ($block->id ?? uniqid());
@endphp

<!-- start: Faq Section -->
<section class="h7-faq section-gap slidebar-stickiy-container">
  <div class="container">
    <div class="row justify-content-between">
      <!-- Left: sticky heading -->
      <div class="col-12 col-lg-4">
        <div class="sec-heading style-2 style-7 slidebar-stickiy">
          <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
            <i class="tji-box"></i> {{ $d['kicker'] ?? 'OUR SOLUTIONS' }}
          </span>
          <h2 class="sec-title text-anim">{{ $d['title'] ?? 'Find answers to the common questions' }}</h2>

          @if(!empty($d['phone_raw']) || !empty($d['phone_display']))
            <a class="number" href="tel:{{ $d['phone_raw'] ?? '18884521505' }}">
              <span class="call-icon"><i class="tji-phone"></i></span>
              <span>{{ $d['phone_display'] ?? '1-888-452-1505' }}</span>
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

            <div class="accordion-item {{ $isOpen ? 'active' : '' }} wow fadeInUp" data-wow-delay=".3s">
              <button class="faq-title {{ $isOpen ? '' : 'collapsed' }}"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#{{ $itemId }}"
                      aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                {{ $q }}
              </button>

              <div id="{{ $itemId }}" class="collapse {{ $isOpen ? 'show' : '' }}" data-bs-parent="#{{ $accId }}">
                <div class="accordion-body faq-text">
                  <p>{{ $a }}</p>
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
