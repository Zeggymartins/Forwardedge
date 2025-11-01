@php
  $d = $block->data ?? [];
  $plans = $d['plans'] ?? [];

  // sensible defaults so it never looks empty
  if (empty($plans)) {
      $plans = [
          [
              'title'      => 'Basic Plan',
              'subtitle'   => 'Essential Business Services',
              'price_naira'=> '99,000',
              'period'     => '/per month',
              'features'   => [
                  'Access to core services',
                  'Limited customer support (email)',
                  '1 project per month',
                  'Basic reporting and analytics',
                  'Standard templates and tools',
                  'Basic performance tracking',
              ],
              'link_text'  => 'Choose Plan',
              'link'       => '#',
          ],
          [
              'title'      => 'Standard Plan',
              'subtitle'   => 'Complete Business Solutions',
              'price_naira'=> '249,000',
              'period'     => '/per month',
              'features'   => [
                  'All features in Basic Plan',
                  'Priority customer support',
                  'Up to 3 projects per month',
                  'Monthly performance reviews',
                  'Collaboration tools for team',
                  'Custom templates',
              ],
              'link_text'  => 'Choose Plan',
              'link'       => '#',
          ],
          [
              'title'      => 'Premium Plan',
              'subtitle'   => 'Advanced Business Services',
              'price_naira'=> '499,000',
              'period'     => '/per month',
              'features'   => [
                  'All features in Standard Plan',
                  'Dedicated account manager',
                  'Tailored strategy sessions',
                  'Quarterly performance audits',
                  'Priority support',
                  '24/7 emergency service',
              ],
              'link_text'  => 'Choose Plan',
              'link'       => '#',
          ],
      ];
  }

  // unique accordion id so multiple pricing blocks on a page won’t clash
  $accId = 'pricing-' . ($block->id ?? uniqid());
@endphp

<section class="tj-pricing-section-2 section-gap section-separator slidebar-stickiy-container">
  <div class="container">
    <div class="row">
      {{-- LEFT: Accordion --}}
      <div class="col-lg-8 order-2 order-lg-1">
        <div class="accordion tj-faq pricing-accordion" id="{{ $accId }}">
          @foreach($plans as $i => $p)
            @php
              $itemId   = $accId . '-' . ($i+1);
              $isOpen   = $i === 0;
              $title    = $p['title'] ?? 'Plan';
              $subtitle = $p['subtitle'] ?? '';
              $price    = $p['price_naira'] ?? '0';
              // $period   = $p['period'] ?? '/per month';
              $features = $p['features'] ?? [];
              $link     = $p['link'] ?? '#';
              $linkText = $p['link_text'] ?? 'Choose Plan';
            @endphp

            <div class="accordion-item pricing-box wow fadeInUp {{ $isOpen ? 'active' : '' }}" data-wow-delay=".3s">
              <button class="faq-title {{ $isOpen ? '' : 'collapsed' }}"
                      type="button"
                      data-bs-toggle="collapse"
                      data-bs-target="#{{ $itemId }}"
                      aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                {{ $title }}
              </button>

              <div id="{{ $itemId }}" class="collapse {{ $isOpen ? 'show' : '' }}" data-bs-parent="#{{ $accId }}">
                <div class="accordion-body pricing-inner">
                  <div class="pricing-header">
                    <div class="pricing-top">
                      <div class="package-desc">
                        @if($subtitle)<p>{{ $subtitle }}</p>@endif
                      </div>

                      <div class="package-price">
                        <span class="package-currency">₦</span>
                        <span class="price-number">{{ $price }}</span>
                        
                        {{-- <span class="package-period">{{ $period }}</span> --}}
                      </div>
                    </div>

                    <div class="pricing-btn">
                      <a class="text-btn" href="{{ $link }}">
                        <span class="btn-text"><span>{{ $linkText }}</span></span>
                        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                      </a>
                    </div>
                  </div>

                  @if(!empty($features) && is_array($features))
                    <div class="list-items">
                      <ul>
                        @foreach($features as $f)
                          <li><i class="tji-list"></i>{{ $f }}</li>
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
            <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
              <i class="tji-box"></i>{{ $d['sidebar_kicker'] ?? 'Flexible pricing' }}
            </span>
            <h2 class="sec-title title-anim">{{ $d['title'] ?? 'Our Pricing Plan.' }}</h2>
          </div>

          <p class="desc">{{ $d['desc'] ?? 'Our team is always available to address your concerns, providing quick.' }}</p>

          @if(!empty($d['sidebar_more_link']))
            <div class="d-none d-lg-inline-flex wow fadeInUp" data-wow-delay=".6s">
              <a class="tj-primary-btn" href="{{ $d['link'] }}">
                <span class="btn-text"><span>{{ $d['link_text'] ?? 'More Pricing' }}</span></span>
                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
              </a>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Mobile "More Pricing" button --}}
    @if(!empty($d['sidebar_more_link']))
      <div class="row">
        <div class="col-12">
          <div class="d-lg-none d-flex mt-5">
            <a class="tj-primary-btn" href="{{ $d['link'] }}">
              <span class="btn-text"><span>{{ $d['link_text'] ?? 'More Pricing' }}</span></span>
              <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
            </a>
          </div>
        </div>
      </div>
    @endif
  </div>
</section>
