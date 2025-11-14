@extends('user.master_page')
@section('title', 'Pricing | Forward Edge Consulting')

@section('main')
    @include('user.partials.breadcrumb')

 @php
  // $plan is expected like:
  // [
  //   'title' => 'Standard Plan',
  //   'subtitle' => 'Complete Business Solutions',
  //   'features' => [...],
  //   'period' => '/mo',
  //   'price_naira' => 249000,     // numeric
  //   'price_usd'   => 179.00,     // numeric or null
  //   'page_id' => 123,
  //   'block_id' => 456,
  //   'plan_index' => 1,
  //   'return_url' => 'https://...'
  // ]

  $baseNgn = (float)($plan['price_naira'] ?? 0);
  $baseUsd = isset($plan['price_usd']) ? (float)$plan['price_usd'] : null;

  $fullNgn = $baseNgn;
  $fullUsd = $baseUsd;

  $partialNgn = round($baseNgn * 0.7, 2);
  $partialUsd = $baseUsd !== null ? round($baseUsd * 0.7, 2) : null;

  $period = $plan['period'] ?? '/one-time';
@endphp

<section class="tj-pricing-section-2 section-gap">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="sec-heading text-center">
          <span class="sub-title wow fadeInUp" data-wow-delay=".1s">
            <i class="tji-box"></i>Payment Options
          </span>
          <h2 class="sec-title title-anim">
            Choose Your <span>Plan.</span>
          </h2>
          @if(!empty($plan['title']) || !empty($plan['subtitle']))
            <p class="mt-2 text-muted">
              {{ $plan['title'] ?? '' }}{!! !empty($plan['subtitle']) ? ' — '.e($plan['subtitle']) : '' !!}
            </p>
          @endif
        </div>
      </div>
    </div>

    <div class="row row-gap-4 justify-content-center">
      {{-- Full Payment --}}
      <div class="col-xl-5 col-md-6">
        <div class="pricing-box wow fadeInUp" data-wow-delay=".5s">
          <div class="pricing-header">
            <h4 class="package-name">Full Payment</h4>
            <div class="package-desc">
              <p>Pay 100% upfront and secure your seat instantly.</p>
            </div>

            <div class="package-price text-center">
              <span class="package-currency">₦</span>
              <span class="price-number fw-bold d-inline-block text-wrap fs-2 fs-md-1 fs-lg-1">
                {{ number_format($fullNgn, 2) }}
              </span>
              <span class="package-period d-block small">{{ $period }}</span>
            </div>

            @if($fullUsd !== null)
              <div class="package-price text-center mt-1">
                <span class="package-currency">$</span>
                <span class="price-number fw-semibold">{{ number_format($fullUsd, 2) }}</span>
                <span class="package-period d-block small">{{ $period }}</span>
              </div>
            @endif

            <div class="pricing-btn mt-3">
              <a href="#"
                 class="text-btn choose-plan"
                 data-plan="full"
                 data-amount-ngn="{{ $fullNgn }}"
                 @if($fullUsd !== null) data-amount-usd="{{ $fullUsd }}" @endif
                 data-page-id="{{ $plan['page_id'] ?? '' }}"
                 data-block-id="{{ $plan['block_id'] ?? '' }}"
                 data-plan-index="{{ $plan['plan_index'] ?? '' }}"
                 data-return-url="{{ $plan['return_url'] ?? '' }}">
                <span class="btn-text"><span>Choose Full Plan</span></span>
                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
              </a>
            </div>
          </div>
        </div>
      </div>

      {{-- Partial Payment --}}
      {{-- <div class="col-xl-5 col-md-6">
        <div class="pricing-box active wow fadeInUp" data-wow-delay=".7s">
          <div class="pricing-header">
            <h4 class="package-name">Partial Payment</h4>
            <div class="package-desc">
              <p>Pay 70% now, balance before bootcamp starts.</p>
            </div>

            <div class="package-price text-center">
              <span class="package-currency">₦</span>
              <span class="price-number fw-bold d-inline-block text-wrap fs-2 fs-md-1 fs-lg-1">
                {{ number_format($partialNgn, 2) }}
              </span>
              <span class="package-period d-block small">/initial</span>
            </div>

            @if($partialUsd !== null)
              <div class="package-price text-center mt-1">
                <span class="package-currency">$</span>
                <span class="price-number fw-semibold">{{ number_format($partialUsd, 2) }}</span>
                <span class="package-period d-block small">/initial</span>
              </div>
            @endif

            <div class="pricing-btn mt-3">
              <a href="#"
                 class="text-btn choose-plan"
                 data-plan="partial"
                 data-amount-ngn="{{ $partialNgn }}"
                 @if($partialUsd !== null) data-amount-usd="{{ $partialUsd }}" @endif
                 data-page-id="{{ $plan['page_id'] ?? '' }}"
                 data-block-id="{{ $plan['block_id'] ?? '' }}"
                 data-plan-index="{{ $plan['plan_index'] ?? '' }}"
                 data-return-url="{{ $plan['return_url'] ?? '' }}">
                <span class="btn-text"><span>Choose Partial Plan</span></span>
                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
              </a>
            </div>
          </div>
        </div>
      </div> --}}
    </div>

    {{-- Optional: show selected plan features --}}
    @if(!empty($plan['features']) && is_array($plan['features']))
      <div class="row justify-content-center mt-4">
        <div class="col-lg-10">
          <div class="card border-0 shadow-soft p-5">
            <h6 class="mb-2">What’s included</h6>
            <ul class="mb-0">
              @foreach($plan['features'] as $f)
                <li>{{ $f }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
    @endif
  </div>
</section>



@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function() {
            $('.choose-plan').on('click', function(e) {
                e.preventDefault();

                const $btn = $(this);
                const planType = $btn.data('plan');

                $btn.addClass('disabled');
                const originalText = $btn.find('.btn-text span').text();
                $btn.find('.btn-text span').text('Processing…');

                $.ajax({
                    url: "{{ route('enroll.store') }}",
                    method: "POST",
                    data: {
                        plan_type: planType,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.authorization_url) {
                            window.location.href = response.authorization_url;
                        } else {
                            alert(response.message || 'Unexpected response. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Something went wrong. Please try again.';
                        if (xhr.responseJSON?.error) {
                            msg = xhr.responseJSON.error;
                        }
                        alert(msg);
                    },
                    complete: function() {
                        $btn.removeClass('disabled');
                        $btn.find('.btn-text span').text(originalText);
                    }
                });
            });
        });
    </script>
@endpush
