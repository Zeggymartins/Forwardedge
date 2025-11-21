@php
  use App\Models\CourseContent;
  use App\Models\Course;

  $d = $block->data ?? [];
  $plans = is_array($d['plans'] ?? null) ? $d['plans'] : [];

  $contentIds = collect($plans)->pluck('course_content_id')->filter()->unique()->values();
  $linkedContents = $contentIds->isNotEmpty()
      ? CourseContent::with('course:id,slug,title')->whereIn('id', $contentIds)->get()->keyBy('id')
      : collect();

  $courseIds = collect($plans)->pluck('course_id')->filter()->unique()->values();
  $linkedCourses = $courseIds->isNotEmpty()
      ? Course::select('id', 'slug', 'title')->whereIn('id', $courseIds)->get()->keyBy('id')
      : collect();

  // unique accordion id so multiple pricing blocks on a page won’t clash
  $accId = 'pricing-' . ($block->id ?? uniqid());
@endphp

<section class="tj-pricing-section-2 section-gap section-separator slidebar-stickiy-container pb-rich-text">
  <div class="container">
    <div class="row">
      {{-- LEFT: Accordion --}}
      <div class="col-lg-8 order-2 order-lg-1">
        <div class="accordion tj-faq pricing-accordion" id="{{ $accId }}">
          @foreach($plans as $i => $p)
            @php
              $itemId   = $accId . '-' . ($i+1);
              $isOpen   = $i === 0;
              $title    = $p['title'] ?? null;
              $subtitle = $p['subtitle'] ?? null;
              $period   = $p['period'] ?? null;
              $features = is_array($p['features'] ?? null) ? $p['features'] : [];
              $planContentId = $p['course_content_id'] ?? null;
              $linkedContent = $planContentId ? $linkedContents->get($planContentId) : null;
              $linkedCourse = $linkedContent?->course ?? $linkedCourses->get($p['course_id'] ?? null);
              $moduleLink = null;
              if ($linkedContent && $linkedCourse && $linkedCourse->slug) {
                  $moduleLink = route('shop.details', ['slug' => $linkedCourse->slug, 'content' => $linkedContent->id]);
              } elseif ($linkedCourse && $linkedCourse->slug) {
                  $moduleLink = route('course.show', $linkedCourse->slug);
              }

              // Normalize numbers for reliable output
              $priceNgnRaw = preg_replace('/[^\d.]/', '', (string)($p['price_naira'] ?? ''));
              $priceUsdRaw = preg_replace('/[^\d.]/', '', (string)($p['price_usd']   ?? ''));

              // Nice display versions
              $priceNgnDisp = $priceNgnRaw !== '' ? number_format((float)$priceNgnRaw, 0) : null;
              $priceUsdDisp = $priceUsdRaw !== '' ? number_format((float)$priceUsdRaw, 2) : null;

              // HREF logic:
              //  1) If editor set a 'link', use it.
              //  2) Else, fall back to internal pricing page that reads page+block+plan.
              $editorLink = trim((string)($p['link'] ?? ''));
              $internal   = route('enroll.pricing', [
                                'page'  => $page->id,
                                'block' => $block->id,
                                'plan'  => $i,
                              ]);
              $href       = $moduleLink ?? ($editorLink !== '' ? $editorLink : $internal);
              $linkText   = $p['link_text'] ?? null;
            @endphp
            @continue(blank($title))

            <div class="accordion-item pricing-box wow fadeInUp {{ $isOpen ? 'active' : '' }}" data-wow-delay=".3s">
              <button class="faq-title {{ $isOpen ? '' : 'collapsed' }}"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#{{ $itemId }}"
                      aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                {!! pb_text($title) !!}
              </button>

              <div id="{{ $itemId }}" class="collapse {{ $isOpen ? 'show' : '' }}" data-bs-parent="#{{ $accId }}">
                <div class="accordion-body pricing-inner">
                  <div class="pricing-header">
                    <div class="pricing-top">
                      <div class="package-desc">
                        @if($subtitle)<p>{!! pb_text($subtitle) !!}</p>@endif
                        @if($linkedContent)
                          <p class="text-muted small mb-0">Module: {!! pb_text($linkedContent->title) !!}</p>
                        @endif
                      </div>

                      @if($priceNgnDisp !== null)
                        <div class="package-price me-3">
                          <span class="package-currency">₦</span>
                          <span class="price-number">{{ $priceNgnDisp }}</span>
                          @if($period)<span class="package-period">{!! pb_text($period) !!}</span>@endif
                        </div>
                      @endif

                      @if($priceUsdDisp !== null)
                        <div class="package-price">
                          <span class="package-currency">$</span>
                          <span class="price-number">{{ $priceUsdDisp }}</span>
                          @if($period)<span class="package-period">{!! pb_text($period) !!}</span>@endif
                        </div>
                      @endif
                    </div>

                    @if(!blank($href) && !blank($linkText))
                      <div class="pricing-btn">
                        <a class="text-btn" href="{{ $href }}">
                          <span class="btn-text"><span>{!! pb_text($linkText) !!}</span></span>
                          <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                        </a>
                      </div>
                    @endif
                  </div>

                  @if(!empty($features))
                    <div class="list-items">
                      <ul>
                        @foreach($features as $f)
                          @continue(blank($f))
                          <li><i class="tji-list"></i>{!! pb_text($f) !!}</li>
                        @endforeach
                      </ul>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      {{-- RIGHT: Sidebar --}}
      <div class="col-lg-4 order-1 order-lg-2">
        <div class="content-wrap slidebar-stickiy">
          <div class="sec-heading style-4">
            @if(!blank($d['sidebar_kicker'] ?? null))
              <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
                <i class="tji-box"></i>{!! pb_text($d['sidebar_kicker'] ?? null) !!}
              </span>
            @endif
            @if(!blank($d['title'] ?? null))
              <h2 class="sec-title title-anim">{!! pb_text($d['title'] ?? null) !!}</h2>
            @endif
          </div>

          @if(!blank($d['desc'] ?? null))
            <p class="desc">{!! pb_text($d['desc'] ?? null) !!}</p>
          @endif

          @if(!blank($d['sidebar_more_link'] ?? null) && !blank($d['link_text'] ?? null))
            <div class="d-none d-lg-inline-flex wow fadeInUp" data-wow-delay=".6s">
              <a class="tj-primary-btn" href="{{ $d['sidebar_more_link'] }}">
                <span class="btn-text"><span>{!! pb_text($d['link_text'] ?? null) !!}</span></span>
                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
              </a>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Mobile "More Pricing" button --}}
    @if(!blank($d['sidebar_more_link'] ?? null) && !blank($d['link_text'] ?? null))
      <div class="row">
        <div class="col-12">
          <div class="d-lg-none d-flex mt-5">
            <a class="tj-primary-btn" href="{{ $d['sidebar_more_link'] }}">
              <span class="btn-text"><span>{!! pb_text($d['link_text'] ?? null) !!}</span></span>
              <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
            </a>
          </div>
        </div>
      </div>
    @endif
  </div>
</section>
